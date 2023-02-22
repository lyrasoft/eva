<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\FrontMiddleware;
use App\Module\Front\Home\HomeController;
use App\Module\Front\Home\HomeView;
use Lyrasoft\Luna\Middleware\LocaleMiddleware;
use Lyrasoft\ShopGo\Module\Front\Product\ProductListView;
use Windwalker\Core\Middleware\CsrfMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('front')
    ->namespace('front')
    ->middleware(CsrfMiddleware::class)
    ->middleware(LocaleMiddleware::class)
    ->middleware(FrontMiddleware::class)
    ->register(function (RouteCreator $router) {
        $router->get('home', '/')
            ->view(ProductListView::class);

        $router->load(__DIR__ . '/front/*.php');

        $router->load(__DIR__ . '/packages/front/*.route.php');

        $router->load(__DIR__ . '/custom/front/*.route.php');
    });
