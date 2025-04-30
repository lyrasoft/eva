<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\TokenCoin\Module\Admin\TokenCoinHistory\TokenCoinHistoryController;
use Lyrasoft\TokenCoin\Module\Admin\TokenCoinHistory\TokenCoinHistoryEditView;
use Lyrasoft\TokenCoin\Module\Admin\TokenCoinHistory\TokenCoinHistoryListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('token-coin-history')
    ->extra('menu', ['sidemenu' => 'token_coin_history_list'])
    ->var('type', 'main')
    ->register(function (RouteCreator $router) {
        $router->any('token_coin_history_list', '/token-coin-history/list')
            ->controller(TokenCoinHistoryController::class)
            ->view(TokenCoinHistoryListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('token_coin_history_edit', '/token-coin-history/edit[/{id}]')
            ->controller(TokenCoinHistoryController::class)
            ->view(TokenCoinHistoryEditView::class);
    });
