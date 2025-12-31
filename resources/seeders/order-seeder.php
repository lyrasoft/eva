<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Melo\Entity\Order;
use Lyrasoft\Melo\Entity\OrderItem;
use Lyrasoft\Melo\Enum\InvoiceType;
use Lyrasoft\Melo\Enum\OrderState;
use Lyrasoft\Melo\Enum\Payment;
use Lyrasoft\Melo\Service\OrderService;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\ORM;
use Windwalker\ORM\EntityMapper;

/**
 * Order Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 * @var ApplicationInterface $app
 */
$seeder->import(
    static function () use ($seeder, $orm, $db, $app) {
        $faker = $seeder->faker('zh_TW');

        /** @var EntityMapper<Order> $mapper */
        $mapper = $orm->mapper(Order::class);
        /** @var EntityMapper<OrderItem> $itemMapper */
        $itemMapper = $orm->mapper(OrderItem::class);
        $lessons = $orm->findList(Lesson::class)->all();
        $userIds = $orm->findColumn(User::class, 'id')->dump(true);

        $orderService = $app->service(OrderService::class);

        foreach (range(1, 150) as $i) {
            $item = $mapper->createEntity();

            $item->setUserId((int) $faker->randomElement($userIds));
            $item->setInvoiceNo('T' . $faker->ean8());
            $item->setState($faker->randomElement(OrderState::values()));
            $item->setPayment($faker->randomElement(Payment::values()));
            $item->setNote($faker->paragraph());
            $item->setInvoiceType($faker->randomElement(InvoiceType::values()));

            if ($item->getState() === OrderState::CANCELLED) {
                $item->setCancelledAt($faker->dateTimeThisYear());
            }

            if ($item->getState() === OrderState::FAILED) {
                $item->setExpiredOn($faker->dateTimeThisYear());
            }

            if ($item->getState() === OrderState::PAID) {
                $item->setPaidAt($faker->dateTimeThisYear());
            }

            if ($item->getPayment() === Payment::ATM) {
                $item->setPaymentData(
                    [
                        'bank_code' => '001',
                        'bank_account' => '111111111111',
                    ]
                );
            }

            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+5 days'));
            $item->setCreatedBy($item->getUserId());

            $item = $mapper->createOne($item);

            $total = 0;

            /** @var Lesson $lesson */
            foreach ($faker->randomElements($lessons, 2) as $lesson) {
                $orderItem = $itemMapper->createEntity();

                $orderItem->setOrderId($item->getId());
                $orderItem->setTitle($lesson->getTitle());
                $orderItem->setLessonId($lesson->getId());
                $orderItem->setImage($lesson->getImage());
                $orderItem->setLessonData($lesson->dump(true));
                $orderItem->setPrice($lesson->getPrice());
                $orderItem->setTotal($lesson->getPrice());

                $total += $lesson->getPrice();

                $itemMapper->createOne($orderItem);

                $seeder->outCounting();
            }

            $item->setTotal($total);
            $item->setNo($orderService->createOrderNo($item->getId()));

            $mapper->saveOne($item);
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Order::class);
        $seeder->truncate(OrderItem::class);
    }
);
