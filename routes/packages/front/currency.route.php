<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Currency\CurrencyController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('currency')
    ->register(function (RouteCreator $router) {
        $router->post('currency_switch', '/currency/switch')
            ->controller(CurrencyController::class, 'switch');
    });
