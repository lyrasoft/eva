<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\Checkout\CheckoutController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('checkout')
    ->register(function (RouteCreator $router) {
        $router->post('checkout', '/checkout')
            ->controller(CheckoutController::class, 'checkout');

        $router->any('checkout_shipping', '/checkout/shipping')
            ->controller(CheckoutController::class, 'checkoutShipping')
            ->var('layout', 'shipping');

        $router->any('checkout_payment', '/checkout/payment')
            ->controller(CheckoutController::class, 'checkoutPayment')
            ->var('layout', 'payment');

        $router->post('notify_shipping', '/notify/shipping')
            ->controller(CheckoutController::class, 'checkoutShipping');

        $router->post('notify_payment', '/notify/payment')
            ->controller(CheckoutController::class, 'checkoutPayment');
    });
