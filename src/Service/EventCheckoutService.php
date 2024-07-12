<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\EventAttendingStore;
use App\Data\EventOrderHistory;
use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Enum\AttendState;
use App\Enum\EventOrderState;
use App\Enum\OrderHistoryType;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;

#[Service]
class EventCheckoutService
{
    use TranslatorTrait;

    public function __construct(
        protected ORM $orm,
        protected EventOrderService $orderService,
        protected EventOrderStateService $stateService,
        protected EventAttendeeService $attendeeService,
        protected EventPaymentService $paymentService,
    ) {
        //
    }

    /**
     * @param  EventAttendingStore  $store
     *
     * @return  array{ 0: EventOrder, 1: Collection<EventAttend> }
     */
    public function processOrderAndSave(EventAttendingStore $store): array
    {
        $order = $store->getOrder() ?? throw new \RuntimeException('Missing order to checkout');

        $order = $this->prepareInitialOrderState($order);

        /** @var EventOrder $order */
        $order = $this->orm->createOne($order);

        $order->setNo($this->orderService->createNo($order));

        $this->orm->updateOne($order);

        $attends = $store->getAllAttendEntities();

        foreach ($attends as $i => $attend) {
            $attend = $this->prepareEventAttend($order, $attend);

            /** @var EventAttend $attend */
            $attend = $this->orm->createOne($attend);

            $attend->setNo($this->attendeeService->createNo($order, $attend));

            $this->orm->updateOne($attend);

            $attends[$i] = $attend;
        }

        $store->setOrder($order);

        return [$order, $attends];
    }

    public function prepareInitialOrderState(EventOrder $order): EventOrder
    {
        // Todo: Handle Alternates

        $order->setState(EventOrderState::UNPAID);
        $order->getHistories()
            ->unshift(
                (new EventOrderHistory())
                    ->setState($order->getState())
                    ->setStateText($order->getState()->getTitle($this->lang))
                    ->setType(OrderHistoryType::SYSTEM)
                    ->setMessage('訂單建立')
                    ->setNotify(true)
            );

        return $order;
    }

    public function prepareEventAttend(EventOrder $order, EventAttend $attend): EventAttend
    {
        $attend->setOrderId($order->getId());
        $attend->setState(AttendState::PENDING);

        return $attend;
    }

    public function processPayment(EventAttendingStore $store): mixed
    {
        $order = $store->getOrder();

        if (!$order) {
            throw new \RuntimeException('Order not found in attending store.');
        }

        $gateway = $this->paymentService->getGateway($order->getPayment());

        if (!$gateway) {
            throw new \RuntimeException('付款方式不可用，請聯繫管理員');
        }

        $order->setTransactionNo($gateway->createTransactionNo($order));

        $this->orm->updateOne($order);

        return $gateway->process($store);
    }
}
