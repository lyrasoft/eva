<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Favorite\Module\Front\Favorite\FavoriteController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('favorite')
    ->register(function (RouteCreator $router) {
        $router->any('favorite_ajax', '/favorite/ajax[/{task}]')
            ->controller(FavoriteController::class, 'ajax');
    });
