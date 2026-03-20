<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Investor\Module\Admin\DividendHistory\DividendHistoryController;
use Lyrasoft\Investor\Module\Admin\DividendHistory\DividendHistoryEditView;
use Lyrasoft\Investor\Module\Admin\DividendHistory\DividendHistoryListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('dividend-history')
    ->extra('menu', ['sidemenu' => 'dividend_history_list'])
    ->register(function (RouteCreator $router) {
        $router->any('dividend_history_list', '/dividend-history/list')
            ->controller(DividendHistoryController::class)
            ->view(DividendHistoryListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('dividend_history_edit', '/dividend-history/edit[/{id}]')
            ->controller(DividendHistoryController::class)
            ->view(DividendHistoryEditView::class);
    });
