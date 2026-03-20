<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Investor\Module\Admin\FinancialStatement\FinancialStatementController;
use Lyrasoft\Investor\Module\Admin\FinancialStatement\FinancialStatementEditView;
use Lyrasoft\Investor\Module\Admin\FinancialStatement\FinancialStatementListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('financial-statement')
    ->extra('menu', ['sidemenu' => 'financial_statement_list'])
    ->register(function (RouteCreator $router) {
        $router->any('financial_statement_list', '/financial-statement/list')
            ->controller(FinancialStatementController::class)
            ->view(FinancialStatementListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('financial_statement_edit', '/financial-statement/edit[/{id}]')
            ->controller(FinancialStatementController::class)
            ->view(FinancialStatementEditView::class);
    });
