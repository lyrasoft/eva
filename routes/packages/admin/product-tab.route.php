<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\ProductTab\ProductTabController;
use Lyrasoft\ShopGo\Module\Admin\ProductTab\ProductTabEditView;
use Lyrasoft\ShopGo\Module\Admin\ProductTab\ProductTabListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('product-tab')
    ->extra('menu', ['sidemenu' => 'product_tab_list'])
    ->register(function (RouteCreator $router) {
        $router->any('product_tab_list', '/product-tab/list')
            ->controller(ProductTabController::class)
            ->view(ProductTabListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('product_tab_edit', '/product-tab/edit[/{id}]')
            ->controller(ProductTabController::class)
            ->view(ProductTabEditView::class);
    });
