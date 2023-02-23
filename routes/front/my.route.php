<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\MyDashboard\MyDashboardController;
use App\Module\Front\MyDashboard\MyDashboardView;
use Lyrasoft\Luna\Middleware\LoginRequireMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('my')
    ->prefix('my')
    ->middleware(LoginRequireMiddleware::class)
    ->register(function (RouteCreator $router) {
        $router->any('my', '/')
            ->controller(MyDashboardController::class)
            ->view(MyDashboardView::class);

        $router->load(__DIR__ . '/my/*');
    });
