<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Melo\Entity\MeloOrder;
use Lyrasoft\Melo\Entity\MeloOrderItem;
use Lyrasoft\Melo\Enum\InvoiceType;
use Lyrasoft\Melo\Enum\OrderState;
use Lyrasoft\Melo\Enum\Payment;
use Lyrasoft\Melo\Features\OrderService;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Order Seeder */ class extends AbstractSeeder
{
    #[SeedImport]
    public function import(OrderService $orderService): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<MeloOrder> $mapper */
        $mapper = $this->orm->mapper(MeloOrder::class);
        /** @var EntityMapper<MeloOrderItem> $itemMapper */
        $itemMapper = $this->orm->mapper(MeloOrderItem::class);
        $lessons = $this->orm->findList(Lesson::class)->all();
        $userIds = $this->orm->findColumn(User::class, 'id')->dump(true);

        foreach (range(1, 150) as $i) {
            $item = $mapper->createEntity();

            $item->userId = (int) $faker->randomElement($userIds);
            $item->invoiceNo = 'T' . $faker->ean8();
            $item->state = $faker->randomElement(OrderState::values());
            $item->payment = $faker->randomElement(Payment::values());
            $item->note = $faker->paragraph();
            $item->invoiceType = $faker->randomElement(InvoiceType::values());

            if ($item->state === OrderState::CANCELLED) {
                $item->cancelledAt = $faker->dateTimeThisYear();
            }

            if ($item->state === OrderState::FAILED) {
                $item->expiredOn = $faker->dateTimeThisYear();
            }

            if ($item->state === OrderState::PAID) {
                $item->paidAt = $faker->dateTimeThisYear();
            }

            if ($item->payment === Payment::ATM) {
                $item->paymentData = [
                    'bank_code' => '001',
                    'bank_account' => '111111111111',
                ];
            }

            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+5 days');
            $item->createdBy = $item->userId;

            $item = $mapper->createOne($item);

            $total = 0;

            /** @var Lesson $lesson */
            foreach ($faker->randomElements($lessons, 2) as $lesson) {
                $orderItem = $itemMapper->createEntity();

                $orderItem->orderId = $item->id;
                $orderItem->title = $lesson->title;
                $orderItem->lessonId = $lesson->id;
                $orderItem->image = $lesson->image;
                $orderItem->lessonData = $lesson->dump(true);
                $orderItem->price = $lesson->price;
                $orderItem->total = $lesson->price;

                $total += $lesson->price;

                $itemMapper->createOne($orderItem);

                $this->printCounting();
            }

            $item->total = $total;
            $item->no = $orderService->createOrderNo($item->id);

            $mapper->saveOne($item);
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(MeloOrder::class);
        $this->truncate(MeloOrderItem::class);
    }
};
