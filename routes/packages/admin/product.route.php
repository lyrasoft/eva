<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Product\ProductController;
use Lyrasoft\ShopGo\Module\Admin\Product\ProductEditView;
use Lyrasoft\ShopGo\Module\Admin\Product\ProductListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('product')
    ->extra('menu', ['sidemenu' => 'product_list'])
    ->register(function (RouteCreator $router) {
        $router->any('product_list', '/product/list')
            ->controller(ProductController::class)
            ->view(ProductListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('product_edit', '/product/edit[/{id}]')
            ->controller(ProductController::class)
            ->view(ProductEditView::class);

        $router->any('product_ajax', '/product/ajax[/{task}]')
            ->controller(ProductController::class, 'ajax');
    });
