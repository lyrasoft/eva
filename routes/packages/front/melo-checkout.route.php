<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Checkout\CheckoutController;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */
$router->group('melo_checkout')
    ->register(function (RouteCreator $router) {
        $router->post('melo_checkout', 'lesson/checkout')
            ->controller(CheckoutController::class, 'checkout');

        $router->any('melo_payment_task', 'lesson/payment/task[/{task}]')
            ->controller(CheckoutController::class, 'paymentTask');
    });
