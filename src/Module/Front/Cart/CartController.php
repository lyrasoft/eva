<?php

declare(strict_types=1);

namespace App\Module\Front\Cart;

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Cart\CartData;
use Lyrasoft\ShopGo\Cart\CartParams;
use Lyrasoft\ShopGo\Cart\CartService;
use Lyrasoft\ShopGo\Cart\CartStorage;
use Lyrasoft\ShopGo\Data\CartPricingData;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseAttachment;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Entity\Shipping;
use Lyrasoft\ShopGo\Payment\PaymentService;
use Lyrasoft\ShopGo\Service\AdditionalPurchaseService;
use Lyrasoft\ShopGo\Service\DiscountService;
use Lyrasoft\ShopGo\Service\VariantService;
use Lyrasoft\ShopGo\Shipping\ShippingService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\ORM\ORM;

/**
 * The CartController class.
 */
#[Controller]
class CartController
{
    use TranslatorTrait;

    public function ajax(AppContext $app): mixed
    {
        $task = $app->input('task');

        return $app->call([$this, $task]);
    }

    public function addToCart(AppContext $app, CartStorage $cartStorage): array
    {
        [
            $id,
            $variantId,
            $quantity,
            $attachments,
        ] = $app->input('product_id', 'variant_id', 'quantity', 'attachments')->values();

        /** @var ProductVariant $variant */
        [$product, $variant] = $app->call(
            [$this, 'validateProductVariant'],
            [
                'id' => (int) $id,
                'variantId' => (int) $variantId,
            ]
        );

        $attachments = (array) $attachments;

        if ($attachments !== []) {
            $app->call(
                [$this, 'validateAdditionalPurchase'],
                [
                    'targetProduct' => $product,
                    'attachments' => $attachments,
                ]
            );
        }

        $cartStorage->addToCart($variant->id, (int) $quantity, compact('attachments'));

        return array_values($cartStorage->getStoredItems());
    }

    public function removeItem(AppContext $app, CartService $cartService, CartStorage $cartStorage): bool
    {
        $key = $app->input('key');

        $cartStorage->removeByKey($key);

        return true;
    }

    public function updateQuantities(AppContext $app, CartStorage $cartStorage): bool
    {
        $values = (array) $app->input('values');

        $cartStorage->updateQuantities($values);

        return true;
    }

    public function updateChecks(AppContext $app, CartStorage $cartStorage): bool
    {
        $checks = (array) $app->input('checks');

        $cartStorage->updateChecks($checks);

        return true;
    }

    public function getItems(AppContext $app, CartService $cartService): CartData
    {
        $locationId = $app->input('location_id') ?? 0;
        $shippingId = $app->input('shipping_id') ?? 0;
        $paymentId = $app->input('payment_id') ?? 0;

        return $cartService->getCartDataForCheckout(
            (int) $locationId,
            $shippingId,
            $paymentId,
        );
    }

    public function clearCart(AppContext $app, CartStorage $cartStorage): bool
    {
        $cartStorage->clear();
        $cartStorage->clearCoupons();

        return true;
    }

    public function addCode(
        AppContext $app,
        UserService $userService,
        CartService $cartService,
        CartStorage $cartStorage,
        DiscountService $discountService
    ): bool {
        $code = $app->input('code');

        if (!$code) {
            throw new \RuntimeException(
                $this->trans('shopgo.cart.message.code.not.found')
            );
        }

        $cartData = $cartService->getCartData();

        if (!count($cartData->getItems())) {
            throw new \RuntimeException($this->trans('shopgo.cart.message.no.products'), 403);
        }

        $user = $userService->getUser();

        $discounts = $discountService->findCodeDiscountsAndCoupons($code, $user->isLogin() ? $user : null);

        $data = new CartPricingData();
        $data->totals = $cartData->totals;
        $data->total = $cartData->totals['total'];
        $data->cartData = $cartData;

        $matched = null;

        foreach ($discounts as $discount) {
            if (!$discountService->matchDiscount($discount, $data)) {
                continue;
            }

            $matched = $discount;
            break;
        }

        if (!$matched) {
            throw new \RuntimeException($this->trans('shopgo.cart.message.no.available.codes'), 404);
        }

        $cartStorage->addCoupon($matched->id);

        return true;
    }

    public function removeCode(AppContext $app, CartStorage $cartStorage): bool
    {
        $id = (int) $app->input('id');

        if (!$id) {
            return false;
        }

        $cartStorage->removeCoupon($id);

        return true;
    }

    /**
     * @param  int  $id
     * @param  int  $variantId
     * @param  ORM  $orm
     *
     * @return  array{ 0: Product, 1: ProductVariant }
     */
    public function validateProductVariant(int $id, int $variantId, ORM $orm): array
    {
        $product = $orm->mustFindOne(Product::class, $id);

        $variant = $orm->mustFindOne(ProductVariant::class, ['product_id' => $id, 'id' => $variantId]);

        return [$product, $variant];
    }

    public function validateAdditionalPurchase(
        Product $targetProduct,
        array $attachments,
        ORM $orm,
        AdditionalPurchaseService $additionalPurchaseService
    ): void {
        $attachmentIds = array_keys($attachments);

        $attachItems = $orm->findList(AdditionalPurchaseAttachment::class, ['id' => $attachmentIds])->all();

        foreach ($attachItems as $attachItem) {
            [$product, $variant] = $additionalPurchaseService->validateAttachment($attachItem, $targetProduct);

            if (VariantService::isOutOfStock($variant, $product)) {
                throw new ValidateFailException(
                    sprintf(
                        "Attachment: %s - %s (%d) is out of stock",
                        $product->title,
                        $variant->title,
                        $variant->id
                    )
                );
            }
        }
    }

    public function getLocation(AppContext $app, ORM $orm): ?Location
    {
        $id = $app->input('id');

        return $orm->findOne(Location::class, $id);
    }

    public function shippings(
        AppContext $app,
        ORM $orm,
        ShippingService $shippingService,
        CartService $cartService,
    ): iterable {
        $locationId = (int) $app->input('location_id');

        $location = $orm->findOne(Location::class, $locationId);

        if (!$location || !$location->canShip) {
            return [];
        }

        $cartData = $cartService->getCartData(
            new CartParams(
                locationId: $location->id
            )
        );

        /** @var Product[] $products */
        $products = [];

        foreach ($cartData->getCheckedItems() as $item) {
            /** @var Product $product */
            $product = $item->product->getData();
            $products[$product->id] = $product;

            foreach ($item->attachments as $attachment) {
                /** @var Product $product */
                $product = $attachment->product->getData();
                $products[$product->id] = $product;
            }
        }

        $shippings = $shippingService->getShippings($location, $products);

        $validShippings = [];

        foreach ($shippings as $shipping) {
            $instance = $shippingService->createTypeInstance($shipping);

            if (!$instance) {
                continue;
            }

            if (!$instance->inPriceRange($cartData->totals['total'])) {
                continue;
            }

            if (!$instance->isSupported($cartData)) {
                continue;
            }

            $fee = $instance->computeShippingFee($cartData, $cartData->totals['total']);

            $shipping->fee = $fee;

            $shipping->checkoutFormComponent = $instance->form($location);

            $validShippings[] = $shipping;
        }

        return $validShippings;
    }

    public function payments(
        AppContext $app,
        ORM $orm,
        PaymentService $paymentService,
        CartService $cartService,
    ): iterable {
        $locationId = (int) $app->input('location_id');
        $shippingId = (int) $app->input('shipping_id');

        $location = $orm->findOne(Location::class, $locationId);

        if (!$location || !$location->canShip) {
            return [];
        }

        $cartData = $cartService->getCartData(new CartParams(locationId: $location->id));
        $shipping = $orm->findOne(Shipping::class, $shippingId);

        if (!$shipping) {
            return [];
        }

        $payments = $paymentService->getPayments($location, $shipping);
        $validPayments = [];

        foreach ($payments as $payment) {
            $instance = $paymentService->createTypeInstance($payment);

            if (!$instance) {
                continue;
            }

            if (!$instance->inPriceRange($cartData->totals['total'])) {
                continue;
            }

            if (!$instance->isSupported($cartData)) {
                continue;
            }

            $payment->checkoutFormComponent = $instance->form($location);

            $validPayments[] = $payment;
        }

        return $validPayments;
    }
}
