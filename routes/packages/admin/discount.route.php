<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Discount\DiscountController;
use Lyrasoft\ShopGo\Module\Admin\Discount\DiscountEditView;
use Lyrasoft\ShopGo\Module\Admin\Discount\DiscountListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('discount')
    ->extra('menu', ['sidemenu' => 'discount_list'])
    ->register(function (RouteCreator $router) {
        $router->any('discount_list', '/discount/list')
            ->controller(DiscountController::class)
            ->view(DiscountListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('discount_edit', '/discount/edit[/{id}]')
            ->controller(DiscountController::class)
            ->view(DiscountEditView::class);

        $router->any('discount_ajax', '/discount/ajax[/{task}]')
            ->controller(DiscountController::class, 'ajax');
    });
