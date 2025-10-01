<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\ProductAttributeGroup\ProductAttributeGroupController;
use Lyrasoft\ShopGo\Module\Admin\ProductAttributeGroup\ProductAttributeGroupEditView;
use Lyrasoft\ShopGo\Module\Admin\ProductAttributeGroup\ProductAttributeGroupListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('product-attribute-group')
    ->extra('menu', ['sidemenu' => 'product_attribute_group_list'])
    ->register(function (RouteCreator $router) {
        $router->any('product_attribute_group_list', '/product-attribute-group/list')
            ->controller(ProductAttributeGroupController::class)
            ->view(ProductAttributeGroupListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('product_attribute_group_edit', '/product-attribute-group/edit[/{id}]')
            ->controller(ProductAttributeGroupController::class)
            ->view(ProductAttributeGroupEditView::class);
    });
