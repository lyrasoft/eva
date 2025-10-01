<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\AdditionalPurchase\AdditionalPurchaseController;
use Lyrasoft\ShopGo\Module\Admin\AdditionalPurchase\AdditionalPurchaseEditView;
use Lyrasoft\ShopGo\Module\Admin\AdditionalPurchase\AdditionalPurchaseListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('additional-purchase')
    ->extra('menu', ['sidemenu' => 'additional_purchase_list'])
    ->register(function (RouteCreator $router) {
        $router->any('additional_purchase_list', '/additional-purchase/list')
            ->controller(AdditionalPurchaseController::class)
            ->view(AdditionalPurchaseListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('additional_purchase_edit', '/additional-purchase/edit[/{id}]')
            ->controller(AdditionalPurchaseController::class)
            ->view(AdditionalPurchaseEditView::class);

        $router->any('additional_purchase_ajax', '/additional-purchase/ajax[/{task}]')
            ->controller(AdditionalPurchaseController::class, 'ajax');
    });
