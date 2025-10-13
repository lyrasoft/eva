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
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use function Windwalker\collect;

return new /** EventOrder Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(
        EventOrderService $orderService,
        EventAttendeeService $attendeeService,
        InvoiceService $invoiceService,
        EventBookingPackage $eventBooking,
        LangService $lang,
    ): void {
        $lang->loadAllFromVendor(EventBookingPackage::class, 'ini');

        $faker = $this->faker($eventBooking->config('fixtures.locale') ?: 'en_US');

        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        $events = $this->orm->findList(Event::class)
            ->all()
            ->keyBy('id')
            ->dump();

        $stages = $this->orm->findList(EventStage::class)
            ->all()
            ->dump();

        $planGroup = $this->orm->findList(EventPlan::class)
            ->all()
            ->groupBy('stageId');

        /** @var EntityMapper<EventOrder> $mapper */
        $mapper = $this->orm->mapper(EventOrder::class);

        /** @var EventStage $stage */
        foreach ($stages as $stage) {
            /** @var Event $event */
            $event = $events[$stage->eventId];

            $plans = $planGroup[$stage->id] ?? collect();
            $attends = [];
            $total = BigDecimal::zero();

            foreach (range(1, 3) as $a) {
                /** @var EventPlan $plan */
                $plan = $faker->randomElement($plans->dump());

                $attend = new EventAttend();
                $attend->eventId = $event->id;
                $attend->stageId = $stage->id;
                $attend->planId = $plan->id;
                $attend->planTitle = $plan->title;
                $attend->price = $plan->price;
                $attend->name = $faker->firstName() . $faker->lastName();
                $attend->nick = $faker->firstName();
                $attend->mobile = $faker->numerify('09########');
                $attend->phone = $faker->numerify('02-####-####');
                $attend->address = $faker->address();
                $attend->state = AttendState::PENDING;
                $attend->screenshots = [
                    'plan' => $plan
                ];

                $total = $total->plus($attend->price);

                $attends[] = $attend;
            }

            $item = $mapper->createEntity();

            $item->userId = (int) $faker->randomElement($userIds);
            $item->eventId = $event->id;
            $item->stageId = $stage->id;
            $item->no = $orderService->createNo($item);
            $item->invoiceType = $faker->randomElement(InvoiceType::cases());
            $invoice = $item->invoiceData
                ->setNo($invoiceService->createNo($item));

            if ($item->invoiceType === InvoiceType::BUSINESS) {
                $invoice->setVat($faker->numerify('########'));
                $invoice->setTitle($faker->company());
            }

            $item->total = $total;
            $item->totals
                ->set(
                    'grand_total',
                    (new EventOrderTotal())
                        ->setTitle('Grand Total')
                        ->setCode('grand_total')
                        ->setValue($total->toFloat())
                        ->setType('total')
                );

            $item->name = $faker->name();
            $item->nick = $faker->firstName();
            $item->mobile = $faker->numerify('09########');
            $item->phone = $faker->numerify('02-####-####');
            $item->address = $faker->address();
            $item->state = EventOrderState::DONE;
            $item->histories
                ->push(
                    new EventOrderHistory()
                        ->setState(EventOrderState::UNPAID)
                        ->setStateText(EventOrderState::UNPAID->getTitle($lang))
                        ->setType(OrderHistoryType::SYSTEM)
                        ->setMessage('Order Created')
                );
            // $item->setPayment('atm');
            $item->expiredAt = '+14days';
            $item->screenshots = [
                'event' => $event,
                'stage' => $stage
            ];

            /** @var EventOrder $item */
            $item = $mapper->createOne($item);

            foreach ($attends as $attend) {
                $attend->orderId = $item->id;
                $attend->no = $attendeeService->createNo($item, $attend);

                $this->orm->createOne($attend);

                $this->printCounting();
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(EventOrder::class, EventAttend::class);
    }
};
