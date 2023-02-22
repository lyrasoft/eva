<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Wishlist\WishlistController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('wishlist')
    ->register(function (RouteCreator $router) {
        $router->any('wishlist_ajax', '/wishlist/ajax[/{task}]')
            ->controller(WishlistController::class, 'ajax');
    });
