<?php

declare(strict_types=1);

namespace App\Seeder;

use Brick\Math\BigDecimal;
use Lyrasoft\EventBooking\Data\EventOrderHistory;
use Lyrasoft\EventBooking\Data\EventOrderTotal;
use Lyrasoft\EventBooking\Entity\Event;
use Lyrasoft\EventBooking\Entity\EventAttend;
use Lyrasoft\EventBooking\Entity\EventOrder;
use Lyrasoft\EventBooking\Entity\EventPlan;
use Lyrasoft\EventBooking\Entity\EventStage;
use Lyrasoft\EventBooking\Enum\AttendState;
use Lyrasoft\EventBooking\Enum\EventOrderState;
use Lyrasoft\EventBooking\Enum\InvoiceType;
use Lyrasoft\EventBooking\Enum\OrderHistoryType;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\EventBooking\Service\EventAttendeeService;
use Lyrasoft\EventBooking\Service\EventOrderService;
use Lyrasoft\EventBooking\Service\InvoiceService;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Windwalker\collect;

/**
 * EventOrder Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        EventOrderService $orderService,
        EventAttendeeService $attendeeService,
        InvoiceService $invoiceService,
        EventBookingPackage $eventBooking,
        LangService $lang,
    ) use (
        $seeder,
        $orm,
        $db
    ) {
        $lang->loadAllFromVendor(EventBookingPackage::class, 'ini');

        $faker = $seeder->faker($eventBooking->config('fixtures.locale') ?: 'en_US');

        $userIds = $orm->findColumn(User::class, 'id')->dump();
        $events = $orm->findList(Event::class)
            ->all()
            ->keyBy('id')
            ->dump();

        $stages = $orm->findList(EventStage::class)
            ->all()
            ->dump();

        $planGroup = $orm->findList(EventPlan::class)
            ->all()
            ->groupBy('stageId');

        /** @var EntityMapper<EventOrder> $mapper */
        $mapper = $orm->mapper(EventOrder::class);

        /** @var EventStage $stage */
        foreach ($stages as $stage) {
            /** @var Event $event */
            $event = $events[$stage->getEventId()];

            $plans = $planGroup[$stage->getId()] ?? collect();
            $attends = [];
            $total = BigDecimal::zero();

            foreach (range(1, 3) as $a) {
                /** @var EventPlan $plan */
                $plan = $faker->randomElement($plans->dump());

                $attend = new EventAttend();
                $attend->setEventId($event->getId());
                $attend->setStageId($stage->getId());
                $attend->setPlanId($plan->getId());
                $attend->setPlanTitle($plan->getTitle());
                $attend->setPrice($plan->getPrice());
                $attend->setName($faker->firstName() . $faker->lastName());
                $attend->setNick($faker->firstName());
                $attend->setMobile($faker->numerify('09########'));
                $attend->setPhone($faker->numerify('02-####-####'));
                $attend->setAddress($faker->address());
                $attend->setState(AttendState::PENDING);
                $attend->setScreenshots(
                    [
                        'plan' => $plan
                    ]
                );

                $total = $total->plus($attend->getPrice());

                $attends[] = $attend;
            }

            $item = $mapper->createEntity();

            $item->setUserId((int) $faker->randomElement($userIds));
            $item->setEventId($event->getId());
            $item->setStageId($stage->getId());
            $item->setNo($orderService->createNo($item));
            $item->setInvoiceType($faker->randomElement(InvoiceType::cases()));
            $invoice = $item->getInvoiceData()
                ->setNo($invoiceService->createNo($item));

            if ($item->getInvoiceType() === InvoiceType::BUSINESS) {
                $invoice->setVat($faker->numerify('########'));
                $invoice->setTitle($faker->company());
            }

            $item->setTotal($total);
            $item->getTotals()
                ->set(
                    'grand_total',
                    (new EventOrderTotal())
                    ->setTitle('Grand Total')
                    ->setCode('grand_total')
                    ->setValue($total->toFloat())
                    ->setType('total')
                );

            $item->setName($faker->name());
            $item->setNick($faker->firstName());
            $item->setMobile($faker->numerify('09########'));
            $item->setPhone($faker->numerify('02-####-####'));
            $item->setAddress($faker->address());
            $item->setState(EventOrderState::DONE);
            $item->getHistories()
                ->push(
                    (new EventOrderHistory())
                        ->setState(EventOrderState::UNPAID)
                        ->setStateText(EventOrderState::UNPAID->getTitle($lang))
                        ->setType(OrderHistoryType::SYSTEM)
                        ->setMessage('Order Created')
                );
            // $item->setPayment('atm');
            $item->setExpiredAt('+14days');
            $item->setScreenshots(
                [
                    'event' => $event,
                    'stage' => $stage
                ]
            );

            /** @var EventOrder $item */
            $item = $mapper->createOne($item);

            foreach ($attends as $attend) {
                $attend->setOrderId($item->getId());
                $attend->setNo($attendeeService->createNo($item, $attend));

                $orm->createOne($attend);

                $seeder->outCounting();
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(EventOrder::class, EventAttend::class);
    }
);
