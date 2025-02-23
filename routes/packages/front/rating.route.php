<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Feedback\Module\Front\Rating\RatingController;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('rating')
    ->register(function (RouteCreator $router) {
        $router->any('rating_ajax', '/rating/ajax[/{task}]')
            ->controller(RatingController::class, 'ajax')
            ->middleware(JsonApiMiddleware::class);
    });
