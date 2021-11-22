<?php

namespace App\Routes;

use App\Module\Front\Portfolio\PortfolioItemView;
use App\Module\Front\Portfolio\PortfolioListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('portfolio')
    ->register(function (RouteCreator $router) {
        $router->get('portfolio_category', '/portfolios[/{path:.+}]')
            ->view(PortfolioListView::class);

        $router->get('portfolio_item', '/portfolio/{id:\d+}-{alias}')
            ->view(PortfolioItemView::class);
    });
