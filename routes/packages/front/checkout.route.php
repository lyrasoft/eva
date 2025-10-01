<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Checkout\CheckoutController;
use Lyrasoft\ShopGo\Module\Front\Checkout\CheckoutView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('checkout')
    ->register(function (RouteCreator $router) {
        $router->any('checkout', '/checkout')
            ->controller(CheckoutController::class)
            ->postHandler('checkout')
            ->view(CheckoutView::class);

        $router->any('checkout_shipping', '/checkout/shipping')
            ->controller(CheckoutController::class, 'checkoutShipping')
            ->var('layout', 'shipping');

        $router->any('checkout_payment', '/checkout/payment')
            ->controller(CheckoutController::class, 'checkoutPayment')
            ->var('layout', 'payment');

        $router->any('shipping_task', '/shipping/task/{task}')
            ->controller(CheckoutController::class, 'shippingTask');

        $router->any('payment_task', '/payment/task/{task}')
            ->controller(CheckoutController::class, 'paymentTask');
    });
