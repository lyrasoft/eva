<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Cart\CartService;
use Lyrasoft\ShopGo\Cart\Contract\CheckoutProcessLayoutInterface;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\Payment;
use Lyrasoft\ShopGo\Entity\Shipping;
use Lyrasoft\ShopGo\Enum\InvoiceType;
use Lyrasoft\ShopGo\Payment\PaymentService;
use Lyrasoft\ShopGo\Service\CheckoutService;
use Lyrasoft\ShopGo\Service\LocationService;
use Lyrasoft\ShopGo\Shipping\ShippingService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Psr\Http\Message\UriInterface;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\View\View;
use Windwalker\Http\Response\RedirectResponse;
use Windwalker\ORM\ORM;

use function Windwalker\response;

/**
 * The CheckoutController class.
 */
#[Controller(
    config: __DIR__ . '/checkout.config.php'
)]
class CheckoutController
{
    public function checkout(
        AppContext $app,
        ORM $orm,
        Navigator $nav,
        UserService $userService,
        ShopGoPackage $shopGo,
        CartService $cartService,
        CheckoutService $checkoutService
    ) {
        $allowAnonymous = $shopGo->config('checkout.allow_anonymous') ?? false;
        /** @var User $user */
        $user = $userService->getUser();

        if (!$allowAnonymous && !$user->isLogin()) {
            return $nav->to('cart');
        }

        $order = $orm->getDb()->transaction(
            function () use ($cartService, $user, $app, $checkoutService) {
                $order = new Order();

                $checkout = (array) $app->input('checkout');

                $payment = (array) $checkout['payment'];
                $shipping = (array) $checkout['shipping'];

                if ($shipping['sync'] ?? false) {
                    $shipping = $payment;
                }

                $addressId = $payment['address_id'] ?? null;

                $paymentLocation = $checkoutService->prepareAddressData(
                    (int) $addressId,
                    $payment,
                    $order->getPaymentData(),
                    $user
                );

                $shippingLocation = $checkoutService->prepareAddressData(
                    (int) $addressId,
                    $shipping,
                    $order->getShippingData(),
                    $user
                );

                $order->setUserId($user->getId());
                $order->setPaymentId((int) $payment['id']);
                $order->setShippingId((int) $shipping['id']);
                $order->setNote($checkout['note'] ?? '');

                if ($order->getPaymentData()->getVat()) {
                    $order->setInvoiceType(InvoiceType::COMPANY());
                } else {
                    $order->setInvoiceType(InvoiceType::IDV());
                }

                $cartData = $cartService->getCartData(
                    [
                        'shipping_id' => $shipping['id'] ?? 0,
                        'payment_id' => $payment['id'] ?? 0,
                        'location_id' => $shippingLocation->getId(),
                    ],
                    CartService::FOR_UPDATE
                );

                return $order = $checkoutService->createOrder($order, $cartData);
            }
        );

        show($order);
    }

    public function checkoutShipping(AppContext $app, ORM $orm, Navigator $nav, ShippingService $shippingService)
    {
        $checkout = $app->input('checkout') ?? [];

        $shippingId = $checkout['shipping']['id'] ?? 0;

        if (!$shippingId) {
            throw new ValidateFailException('No shipping ID');
        }

        $shipping = $orm->mustFindOne(Shipping::class, $shippingId);
        $shippingInstance = $shippingService->createTypeInstance($shipping);

        if (!$shippingInstance instanceof CheckoutProcessLayoutInterface) {
            return response()->redirect($nav->to('checkout_payment'), 307);
        }

        $result = $shippingInstance->renderProcessLayout($app);

        if (is_string($result)) {
            /** @var View $view */
            $view = $app->make(CheckoutView::class);

            return $view->render(['content' => $result, 'data' => $checkout]);
        }

        if ($result instanceof UriInterface) {
            $result = new RedirectResponse($result, 307);
        }

        if ($result instanceof RedirectResponse) {
            $result = $result->withStatus(307);
        }

        return $result;
    }

    public function checkoutPayment(AppContext $app, ORM $orm, Navigator $nav, PaymentService $paymentService)
    {
        $checkout = $app->input('checkout') ?? [];

        $paymentId = $checkout['payment']['id'] ?? 0;

        if (!$paymentId) {
            throw new ValidateFailException('No payment ID');
        }

        $payment = $orm->mustFindOne(Payment::class, $paymentId);
        $paymentInstance = $paymentService->createTypeInstance($payment);

        if (!$paymentInstance instanceof CheckoutProcessLayoutInterface) {
            return response()->redirect($nav->to('checkout'), 307);
        }

        $result = $paymentInstance->renderProcessLayout($app);

        if (is_string($result)) {
            /** @var View $view */
            $view = $app->make(CheckoutView::class);

            return $view->render(['content' => $result, 'data' => $checkout]);
        }

        if ($result instanceof UriInterface) {
            $result = new RedirectResponse($result, 307);
        }

        if ($result instanceof RedirectResponse) {
            $result = $result->withStatus(307);
        }

        return $result;
    }

    public function shippingNotify()
    {
    }

    public function paymentNotify()
    {
    }
}
