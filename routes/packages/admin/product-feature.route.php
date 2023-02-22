<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\ProductFeature\ProductFeatureController;
use Lyrasoft\ShopGo\Module\Admin\ProductFeature\ProductFeatureEditView;
use Lyrasoft\ShopGo\Module\Admin\ProductFeature\ProductFeatureListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('-product-feature')
    ->extra('menu', ['sidemenu' => '_product_feature_list'])
    ->register(function (RouteCreator $router) {
        $router->any('product_feature_list', '/product-feature/list')
            ->controller(ProductFeatureController::class)
            ->view(ProductFeatureListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('product_feature_edit', '/product-feature/edit[/{id}]')
            ->controller(ProductFeatureController::class)
            ->view(ProductFeatureEditView::class);
    });
