<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Currency\CurrencyController;
use Lyrasoft\ShopGo\Module\Admin\Currency\CurrencyEditView;
use Lyrasoft\ShopGo\Module\Admin\Currency\CurrencyListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('currency')
    ->extra('menu', ['sidemenu' => 'currency_list'])
    ->register(function (RouteCreator $router) {
        $router->any('currency_list', '/currency/list')
            ->controller(CurrencyController::class)
            ->view(CurrencyListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('currency_edit', '/currency/edit[/{id}]')
            ->controller(CurrencyController::class)
            ->view(CurrencyEditView::class);
    });
