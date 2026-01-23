<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Melo\Cart\CartService;
use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Melo\Entity\MeloOrder;
use Lyrasoft\Melo\Entity\MeloOrderItem;
use Lyrasoft\Melo\Entity\MeloOrderTotal;
use Lyrasoft\Melo\Enum\InvoiceType;
use Lyrasoft\Melo\Enum\OrderState;
use Lyrasoft\Melo\Features\LessonCheckoutService;
use Lyrasoft\Melo\Features\OrderService;
use Lyrasoft\Melo\Features\Payment\MeloPaymentInterface;
use Lyrasoft\Melo\Features\Payment\PaymentComposer;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Order Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(
        OrderService $orderService,
        LessonCheckoutService $checkoutService,
        CartService $cartService,
        PaymentComposer $paymentComposer,
        LangService $lang,
    ): void {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<MeloOrder> $mapper */
        $mapper = $this->orm->mapper(MeloOrder::class);
        /** @var EntityMapper<MeloOrderItem> $itemMapper */
        $itemMapper = $this->orm->mapper(MeloOrderItem::class);
        $lessons = $this->orm->findList(Lesson::class)->all();
        $userIds = $this->orm->findColumn(User::class, 'id')->dump(true);
        /** @var MeloPaymentInterface[] $payments */
        $payments = $paymentComposer->getGateways()->dump();

        foreach (range(1, 150) as $i) {
            $item = $mapper->createEntity();

            /** @var MeloPaymentInterface $payment */
            $payment = $faker->randomElement($payments);

            $item->userId = (int) $faker->randomElement($userIds);
            $item->invoiceNo = 'T' . $faker->ean8();
            $item->state = $faker->randomElement(OrderState::cases());
            $item->payment = $payment::getId();
            $item->paymentData->paymentTitle = $payment->getTitle($lang);
            $item->note = $faker->paragraph();
            $item->invoiceType = $faker->randomElement(InvoiceType::cases());

            if ($item->state === OrderState::CANCELLED) {
                $item->cancelledAt = $faker->dateTimeThisYear();
            }

            if ($item->state === OrderState::FAILED) {
                $item->expiredOn = $faker->dateTimeThisYear();
            }

            if ($item->state === OrderState::PAID) {
                $item->paidAt = $faker->dateTimeThisYear();
            }

            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+5 days');
            $item->createdBy = $item->userId;

            $item = $mapper->createOne($item);

            $cartItems = [];

            /** @var Lesson $lesson */
            foreach ($faker->randomElements($lessons, 2) as $lesson) {
                $cartItems[] = $cartService->lessonToCartItem($lesson);
            }

            $cartData = $cartService->itemsToCartData($cartItems);

            $orderItems = $checkoutService->createOrderItems($item, $cartData->items);
            $checkoutService->createOrderTotals($item, $cartData->totals);

            $gt = $cartData->totals->get('grand_total');

            $item->total = $gt->toFloat();
            $item->no = $orderService->createOrderNo($item);

            if ($item->state === OrderState::PAID || $item->state === OrderState::FREE) {
                $orderService->assignOrderLessonsToUser($item, $orderItems->dump());
            }

            $mapper->saveOne($item);
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(MeloOrder::class);
        $this->truncate(MeloOrderItem::class);
        $this->truncate(MeloOrderTotal::class);
    }
};
