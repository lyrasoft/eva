<?php

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
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Data\Collection;
use Windwalker\ORM\EntityMapper;

use function Windwalker\chronos;

return new /** Order Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(
        ShopGoPackage $shopGo,
        CheckoutService $checkoutService,
        CartService $cartService,
        OrderStateService $orderStateService,
        LocationService $locationService
    ): void {
        $faker = $this->faker('en_US');

        /** @var EntityMapper<Order> $mapper */
        $mapper = $this->orm->mapper(Order::class);

        $states = $this->orm->findList(OrderState::class)->all()->dump();
        $addresses = $this->orm->findList(Address::class)->all()->dump();
        $products = $this->orm->findList(Product::class)->all()->dump();
        $payments = $this->orm->findList(Payment::class)->all()->dump();
        $shippings = $this->orm->findList(Shipping::class)->all()->dump();
        $variantGroups = $this->orm->findList(ProductVariant::class)->all()->groupBy('productId');

        // $useFullName = $shopGo->useFullName();
        // $useFullAddress = $shopGo->useFullAddress();

        $created = chronos('-2months');

        $users = $this->orm->findList(User::class)->all()->dump();

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
                $cartItem = new CartItem(
                    variant: $productVariant,
                    mainVariant: $mainVariant,
                    product: $product,
                    priceSet: $productVariant->priceSet,
                    quantity: random_int(1, 5),
                    cover: $productVariant->cover,
                    link: '#'
                );

                // Use props set

                $cartItems[] = $cartItem;
            }

            // Create Cart Data
            $cartData = $cartService->createCartDataFromItems($cartItems, []);

            foreach ($cartData->getItems() as $orderItem) {
                $finalTotal = $orderItem->priceSet['final_total'];

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

            $location = $this->orm->mustFindOne(Location::class, $paymentAddress->locationId);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->paymentId = (string) $payment->id;

            $paymentData = $item->paymentData;
            $paymentData->name = $user->name;
            $paymentData->email = $user->email;
            $paymentData->address1 = $paymentAddress->address1;
            $paymentData->address2 = $paymentAddress->address2;
            $paymentData->addressId = $paymentAddress->id;
            $paymentData->country = $country?->title ?: '';
            $paymentData->state = $state?->title ?: '';
            $paymentData->city = $city?->title ?: '';
            $paymentData->phone = $paymentAddress->phone;
            $paymentData->mobile = $paymentAddress->mobile;
            $paymentData->company = $paymentAddress->company;
            $paymentData->vat = $paymentAddress->vat;

            // Shipping

            /** @var Shipping $shipping */
            $shipping = $faker->randomElement($shippings);
            /** @var Address $shippingAddress */
            $shippingAddress = $faker->randomElement($addresses);

            $location = $this->orm->mustFindOne(Location::class, $shippingAddress->locationId);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->shippingId = (string) $shipping->id;

            $firstName = $shippingAddress->firstname;
            $lastName = $shippingAddress->lastname;

            $shippingData = $item->shippingData;
            $shippingData->name = $firstName . ' ' . $lastName;
            $shippingData->firstname = $firstName;
            $shippingData->lastname = $lastName;
            $shippingData->addressId = $shippingAddress->id;
            $shippingData->address1 = $shippingAddress->address1;
            $shippingData->address2 = $shippingAddress->address2;
            $shippingData->country = $country?->title ?: '';
            $shippingData->state = $state?->title ?: '';
            $shippingData->city = $city?->title ?: '';
            $shippingData->phone = $shippingAddress->phone;
            $shippingData->mobile = $shippingAddress->mobile;
            $shippingData->note = $faker->sentence();

            // Invoice
            $item->invoiceType = $faker->randomElement(InvoiceType::cases());

            if ($item->invoiceType === InvoiceType::COMPANY) {
                $item->invoiceData->title = $user->name;
            } else {
                $item->invoiceData->title = $paymentData->company;
                $item->invoiceData->vat = $paymentData->vat;
                $item->invoiceData->mobile = $paymentData->mobile;
            }

            // Date
            $hrOffsets = random_int(8, 36);
            $created = $created->modify("+{$hrOffsets}hours");
            $item->created = $created;

            // Create Order
            $order = $checkoutService->createOrder($item, $cartData);

            // A workaround to prevent relations create twice.
            $order = $this->orm->findOne(Order::class, $order->id);

            // Use State

            /** @var OrderState $state */
            $state = $faker->randomElement($states);

            $order->state = $state;

            $orderStateService->mutateOrderByState(
                $order,
                $state,
                $faker->dateTimeBetween('-1years', 'now')
            );

            $this->orm->updateOne(Order::class, $order);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Order::class, OrderItem::class, OrderTotal::class, OrderHistory::class);
    }
};
