<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\Cart\CartController;
use App\Module\Front\Cart\CartView;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->any('cart', 'cart')
    ->controller(CartController::class)
    ->view(CartView::class);

$router->any('cart_ajax', 'cart/ajax[/{task}]')
    ->controller(CartController::class, 'ajax')
    ->middleware(JsonApiMiddleware::class);
