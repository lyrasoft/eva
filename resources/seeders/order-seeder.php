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
                $variants = $variantGroups[$product->getId()] ?? [];
                $variants = $variants->keyBy('id');
                $mainVariant = $variants[$product->getPrimaryVariantId()];

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
                    ->setPriceSet($productVariant->getPriceSet())
                    ->setCover($productVariant->getCover())
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

            $item->setUserId($user->getId());

            // Payment

            /** @var Payment $payment */
            $payment = $faker->randomElement($payments);
            /** @var Address $paymentAddress */
            $paymentAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $paymentAddress->getLocationId());
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->setPaymentId($payment->getId());

            $paymentData = $item->getPaymentData()
                ->setName($user->getName())
                ->setEmail($user->getEmail())
                ->setAddress1($paymentAddress->getAddress1())
                ->setAddress2($paymentAddress->getAddress2())
                ->setAddressId($paymentAddress->getId())
                ->setCountry($country?->getTitle() ?: '')
                ->setState($state?->getTitle() ?: '')
                ->setCity($city?->getTitle() ?: '')
                ->setPhone($paymentAddress->getPhone())
                ->setMobile($paymentAddress->getMobile())
                ->setCompany($paymentAddress->getCompany())
                ->setVat($paymentAddress->getVat());

            // Shipping

            /** @var Shipping $shipping */
            $shipping = $faker->randomElement($shippings);
            /** @var Address $shippingAddress */
            $shippingAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $shippingAddress->getLocationId());
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->setShippingId($shipping->getId());

            $firstName = $shippingAddress->getFirstname();
            $lastName = $shippingAddress->getLastname();

            $item->getShippingData()
                ->setName($firstName . ' ' . $lastName)
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setAddressId($shippingAddress->getId())
                ->setAddress1($shippingAddress->getAddress1())
                ->setAddress2($shippingAddress->getAddress2())
                ->setCountry($country?->getTitle() ?: '')
                ->setState($state?->getTitle() ?: '')
                ->setCity($city?->getTitle() ?: '')
                ->setPhone($shippingAddress->getPhone())
                ->setMobile($shippingAddress->getMobile())
                ->setNote($faker->sentence());

            // Invoice
            $item->setInvoiceType($faker->randomElement(InvoiceType::cases()));

            if ($item->getInvoiceType() === InvoiceType::COMPANY()) {
                $item->getInvoiceData()
                    ->setTitle($user->getName());
            } else {
                $item->getInvoiceData()
                    ->setTitle($paymentData->getCompany())
                    ->setVat($paymentData->getVat())
                    ->setMobile($paymentData->getMobile());
            }

            // Date
            $hrOffsets = random_int(8, 36);
            $created = $created->modify("+{$hrOffsets}hours");
            $item->setCreated($created);

            // Create Order
            $order = $checkoutService->createOrder($item, $cartData);

            // A workaround to prevent relations create twice.
            $order = $orm->findOne(Order::class, $order->getId());

            // Use State

            /** @var OrderState $state */
            $state = $faker->randomElement($states);

            $order->setState($state);

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
