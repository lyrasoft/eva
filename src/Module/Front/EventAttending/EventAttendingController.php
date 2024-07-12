<?php

declare(strict_types=1);

namespace App\Module\Front\EventAttending;

use App\Data\EventAttendingStore;
use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Service\EventAttendingService;
use App\Service\EventCheckoutService;
use App\Service\EventOrderService;
use App\Service\EventPaymentService;
use App\Service\EventViewService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Manager\Logger;
use Windwalker\ORM\ORM;

use function Windwalker\response;

#[Controller]
class EventAttendingController
{
    public function attending(
        AppContext $app,
        ORM $orm,
        EventAttendingService $eventAttendingService,
        EventViewService $eventViewService
    ): mixed {
        $stageId = (int) $app->input('stageId');

        if (!$stageId) {
            return response()->redirect($app->getSystemUri()->root());
        }

        [$event, $stage] = $eventViewService->checkStageAvailableById($stageId);

        $quantity = $eventAttendingService->getPlanAndQuantity($stageId);

        $eventAttendingService->rememberAttendingData(
            $stageId,
            [
                'quantity' => $quantity,
                'attends' => [],
            ]
        );

        $storage = $eventAttendingService->getAttendingStore($stage);

        // Is empty
        if ($storage->getTotalQuantity() === 0) {
            $app->addMessage('沒有報名資訊', 'warning');

            return $app->getNav()->back();
        }

        return $app->getNav()->to('event_attending')->var('stageId', $stage->getId());
    }

    public function checkout(
        AppContext $app,
        ORM $orm,
        UserService $userService,
        EventAttendingService $eventAttendingService,
        EventViewService $eventViewService,
        EventCheckoutService $eventCheckoutService,
        EventOrderService $eventOrderService
    ) {
        [$stageId, $order, $attends] = $app->input(
            'stageId',
            'order',
            'attends'
        )->values();

        [$event, $stage] = $eventViewService->checkStageAvailableById((int) $stageId);

        // Save to session
        $data = $eventAttendingService->getAttendingDataFromSession($stage->getId());

        if ($data === null) {
            return response()->redirect($app->getSystemUri()->root());
        }

        $data['order'] = $order;
        $data['attends'] = $attends;

        $eventAttendingService->rememberAttendingData($stage->getId(), $data);

        /** @var EventAttendingStore $store */
        $store = $orm->transaction(
            function () use (
                $stage,
                $event,
                $orm,
                $userService,
                $eventAttendingService,
                $eventCheckoutService
            ) {
                $store = $eventAttendingService->getAttendingStore($stage->getId(), true);

                $stage = $store->getStage();

                $user = $userService->getUser();

                // Order
                $orderData = $store->getOrderData();

                $order = new EventOrder();

                if ($user->isLogin()) {
                    $order->setUserId((int) $user->getId());
                }

                $order->setEventId($event->getId());
                $order->setStageId($stage->getId());
                $order->setInvoiceType($orderData['invoice_type']);
                $order->setInvoiceData($orderData['invoice_data']);
                $order->setTotal($store->getGrandTotal()->toFloat());
                $order->setTotals(clone $store->getTotals());
                $order->setName($orderData['name'] ?? '');
                $order->setEmail($orderData['email'] ?? '');
                $order->setNick($orderData['nick'] ?? '');
                $order->setMobile($orderData['mobile'] ?? '');
                $order->setPhone($orderData['phone'] ?? '');
                $order->setAddress($orderData['address'] ?? '');
                $order->setDetails($orderData['details'] ?? []);
                $order->setPayment('atm');
                $order->setScreenshots(compact('event', 'stage'));

                $store->setOrder($order);

                // Prepare Attends
                foreach ($store->getAttendingPlans() as $attendingPlan) {
                    $attends = $attendingPlan->getAttends();
                    $attendEntities = [];
                    $plan = $attendingPlan->getPlan();

                    foreach ($attends as $attendData) {
                        $attend = new EventAttend();
                        $attend->setEventId($event->getId());
                        $attend->setStageId($stage->getId());
                        $attend->setPlanId($plan->getId());
                        $attend->setPlanTitle($plan->getTitle());
                        $attend->setPrice($plan->getPrice());
                        $attend->setName($attendData['name'] ?? '');
                        $attend->setEmail($attendData['email'] ?? '');
                        $attend->setNick($attendData['nick'] ?? '');
                        $attend->setMobile($attendData['mobile'] ?? '');
                        $attend->setPhone($attendData['phone'] ?? '');
                        $attend->setAddress($attendData['address'] ?? '');
                        $attend->setDetails($attendData['details'] ?? []);
                        $attend->setScreenshots(
                            [
                                'plan' => $plan,
                            ]
                        );

                        $attendEntities[] = $attend;
                    }

                    $attendingPlan->setAttendEntities($attendEntities);
                }

                $eventCheckoutService->processOrderAndSave($store);

                return $store;
            }
        );

        // Todo: Event

        $order = $store->getOrder();
        $orderUri = $app->getNav()->to('event_order_item')->var('no', $order->getName());

        try {
            $result = $eventCheckoutService->processPayment($store);

            if (!$result) {
                return $result;
            }
        } catch (\Exception $e) {
            $app->addMessage($e->getMessage(), 'warning');
            Logger::error('checkout-error', $e->getMessage(), ['exception' => $e]);

            return $orderUri;
        }
    }

    public function receiveNotify(AppContext $app, ORM $orm, EventPaymentService $paymentService)
    {
        $id = $app->input('id');

        $order = $orm->findOne(EventOrder::class, $id);

        if (!$order) {
            return 'Order not found';
        }

        $gateway = $paymentService->getGateway($order->getPayment());

        if ($gateway) {
            return 'Gateway not found.';
        }

        return $gateway->receiveNotify($app, $order);
    }
}
