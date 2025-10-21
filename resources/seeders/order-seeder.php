<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\ShopGo\Cart\CartItem;
use Lyrasoft\ShopGo\Cart\CartService;
use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderHistory;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Entity\OrderState;
use Lyrasoft\ShopGo\Entity\OrderTotal;
use Lyrasoft\ShopGo\Entity\Payment;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Entity\Shipping;
use Lyrasoft\ShopGo\Enum\InvoiceType;
use Lyrasoft\ShopGo\Service\CheckoutService;
use Lyrasoft\ShopGo\Service\LocationService;
use Lyrasoft\ShopGo\Service\OrderStateService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Data\Collection;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Windwalker\chronos;

/**
 * Order Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        ShopGoPackage $shopGo,
        CheckoutService $checkoutService,
        CartService $cartService,
        OrderStateService $orderStateService,
        LocationService $locationService,
    ) use (
        $seeder,
        $orm,
        $db
    ) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Order> $mapper */
        $mapper = $orm->mapper(Order::class);

        $states = $orm->findList(OrderState::class)->all()->dump();
        $addresses = $orm->findList(Address::class)->all()->dump();
        $products = $orm->findList(Product::class)->all()->dump();
        $payments = $orm->findList(Payment::class)->all()->dump();
        $shippings = $orm->findList(Shipping::class)->all()->dump();
        $variantGroups = $orm->findList(ProductVariant::class)->all()->groupBy('productId');

        // $useFullName = $shopGo->useFullName();
        // $useFullAddress = $shopGo->useFullAddress();

        $created = chronos('-2months');

        $users = $orm->findList(User::class)->all()->dump();

        foreach (range(1, 50) as $i) {
            /** @var User $user */
            $user = $faker->randomElement($users);

            // Prepare Product / Variants

            /** @var Product[] $products */
            $chosenProducts = $faker->randomElements($products, random_int(3, 5));
            $productVariants = [];

            /** @var Product $product */
            foreach ($chosenProducts as $product) {
                /** @var Collection<ProductVariant> $variants */
                $variants = $variantGroups[$product->id] ?? [];
                $variants = $variants->keyBy('id');
                $mainVariant = $variants[$product->primaryVariantId];

                if (count($variants) > 0) {
                    $variant = $faker->randomElement($variants);
                    $productVariants[] = [$product, $mainVariant, $variant];
                }
            }

            $cartItems = [];

            /** @var ProductVariant $productVariant */
            foreach ($productVariants as [$product, $mainVariant, $productVariant]) {
                $cartItem = new CartItem();
                $cartItem->setProduct($product)
                    ->setVariant($productVariant)
                    ->setMainVariant($mainVariant)
                    ->setQuantity(random_int(1, 5))
                    ->setPriceSet($productVariant->priceSet)
                    ->setCover($productVariant->cover)
                    ->setLink('#');

                $cartItems[] = $cartItem;
            }

            // Create Cart Data
            $cartData = $cartService->createCartDataFromItems($cartItems, []);

            foreach ($cartData->getItems() as $orderItem) {
                $finalTotal = $orderItem->getPriceSet()['final_total'];

                if ($finalTotal->lt(0)) {
                    throw new \RuntimeException(
                        sprintf(
                            'A cartItem is negative price: %s.',
                            (string) $finalTotal
                        )
                    );
                }
            }

            // Start Create Order
            $item = $mapper->createEntity();

            $item->userId = $user->id;

            // Payment

            /** @var Payment $payment */
            $payment = $faker->randomElement($payments);
            /** @var Address $paymentAddress */
            $paymentAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $paymentAddress->locationId);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->paymentId = (string) $payment->id;

            $paymentData = $item->paymentData
                ->setName($user->name)
                ->setEmail($user->email)
                ->setAddress1($paymentAddress->address1)
                ->setAddress2($paymentAddress->address2)
                ->setAddressId($paymentAddress->id)
                ->setCountry($country?->title ?: '')
                ->setState($state?->title ?: '')
                ->setCity($city?->title ?: '')
                ->setPhone($paymentAddress->phone)
                ->setMobile($paymentAddress->mobile)
                ->setCompany($paymentAddress->company)
                ->setVat($paymentAddress->vat);

            // Shipping

            /** @var Shipping $shipping */
            $shipping = $faker->randomElement($shippings);
            /** @var Address $shippingAddress */
            $shippingAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $shippingAddress->locationId);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->shippingId = (string) $shipping->id;

            $firstName = $shippingAddress->firstname;
            $lastName = $shippingAddress->lastname;

            $item->shippingData
                ->setName($firstName . ' ' . $lastName)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setAddressId($shippingAddress->id)
                ->setAddress1($shippingAddress->address1)
                ->setAddress2($shippingAddress->address2)
                ->setCountry($country?->title ?: '')
                ->setState($state?->title ?: '')
                ->setCity($city?->title ?: '')
                ->setPhone($shippingAddress->phone)
                ->setMobile($shippingAddress->mobile)
                ->setNote($faker->sentence());

            // Invoice
            $item->invoiceType = $faker->randomElement(InvoiceType::cases());

            if ($item->invoiceType === InvoiceType::COMPANY) {
                $item->invoiceData
                    ->setTitle($user->name);
            } else {
                $item->invoiceData
                    ->setTitle($paymentData->getCompany())
                    ->setVat($paymentData->getVat())
                    ->setMobile($paymentData->getMobile());
            }

            // Date
            $hrOffsets = random_int(8, 36);
            $created = $created->modify("+{$hrOffsets}hours");
            $item->created = $created;

            // Create Order
            $order = $checkoutService->createOrder($item, $cartData);

            // A workaround to prevent relations create twice.
            $order = $orm->findOne(Order::class, $order->id);

            // Use State

            /** @var OrderState $state */
            $state = $faker->randomElement($states);

            $order->state = $state;

            $orderStateService->mutateOrderByState(
                $order,
                $state,
                $faker->dateTimeBetween('-1years', 'now')
            );

            $orm->updateOne(Order::class, $order);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Order::class, OrderItem::class, OrderTotal::class, OrderHistory::class);
    }
);
