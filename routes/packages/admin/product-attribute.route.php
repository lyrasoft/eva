<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\ProductAttribute\ProductAttributeController;
use Lyrasoft\ShopGo\Module\Admin\ProductAttribute\ProductAttributeEditView;
use Lyrasoft\ShopGo\Module\Admin\ProductAttribute\ProductAttributeListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('product-attribute')
    ->extra('menu', ['sidemenu' => 'product_attribute_list'])
    ->register(function (RouteCreator $router) {
        $router->any('product_attribute_list', '/product-attribute/list')
            ->controller(ProductAttributeController::class)
            ->view(ProductAttributeListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('product_attribute_edit', '/product-attribute/edit[/{id}]')
            ->controller(ProductAttributeController::class)
            ->view(ProductAttributeEditView::class);
    });
