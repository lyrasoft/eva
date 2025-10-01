<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Cart\CartController;
use Lyrasoft\ShopGo\Module\Front\Cart\CartView;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('my')
    ->prefix('my')
    ->register(
        function (RouteCreator $router) {
            $router->load(__DIR__ . '/my/*.php');
        }
    );
