<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\Product\ProductController;
use App\Module\Front\Product\ProductItemView;
use App\Module\Front\Product\ProductListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('product')
    ->register(function (RouteCreator $router) {
        $router->any('product_list', '/products[/{path:.+}]')
            ->controller(ProductController::class)
            ->view(ProductListView::class);

        $router->any('product_item', '/product/item/{id:\d+}-{alias}')
            ->controller(ProductController::class)
            ->view(ProductItemView::class);

        $router->any('product_ajax', '/product/ajax[/{task}]')
            ->controller(ProductController::class, 'ajax');
    });
