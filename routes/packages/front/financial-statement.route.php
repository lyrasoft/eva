<?php

declare(strict_types=1);

namespace Lyrasoft\Investor\Routes;

use Lyrasoft\Investor\Module\Front\DividendHistory\DividendHistoryView;
use Lyrasoft\Investor\Module\Front\FinancialReport\FinancialReportView;
use Lyrasoft\Investor\Module\Front\MonthlyReport\MonthlyReportView;
use Lyrasoft\Investor\Module\Front\Shareholder\ShareholderView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('investor')
    ->register(function (RouteCreator $router) {
        $router->any('investor_monthly_report', '/investor/financial/monthly[/{alias}]')
            ->view(FinancialReportView::class)
            ->var('layout', 'monthly');

        $router->any('investor_season_report', '/investor/financial/season')
            ->view(FinancialReportView::class)
            ->var('layout', 'season');

        $router->any('investor_annual_report', '/investor/financial/annual')
            ->view(FinancialReportView::class)
            ->var('layout', 'annual');
    });
