<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Address\AddressController;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

// $router->any('cart', 'cart')
//     ->controller(AddressController::class)
//     ->view(CartView::class);

$router->any('address_ajax', 'address/ajax[/{task}]')
    ->controller(AddressController::class, 'ajax')
    ->middleware(JsonApiMiddleware::class);
