<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Shipping\ShippingController;
use Lyrasoft\ShopGo\Module\Admin\Shipping\ShippingEditView;
use Lyrasoft\ShopGo\Module\Admin\Shipping\ShippingListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('shipping')
    ->extra('menu', ['sidemenu' => 'shipping_list'])
    ->register(function (RouteCreator $router) {
        $router->any('shipping_list', '/shipping/list')
            ->controller(ShippingController::class)
            ->view(ShippingListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('shipping_edit', '/shipping/edit[/{id}]')
            ->controller(ShippingController::class)
            ->view(ShippingEditView::class);
    });
