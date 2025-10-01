<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Front\Wishlist\WishlistListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('wishlist')
    ->register(function (RouteCreator $router) {
        $router->any('wishlist', '/wishlist')
            ->view(WishlistListView::class);
    });
