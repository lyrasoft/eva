<?php

declare(strict_types=1);

namespace App\Routes;

use Windwalker\Core\Middleware\CanonicalizeMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('web')
    ->middleware(
        CanonicalizeMiddleware::class,
        forceSsl: (bool) env('FORCE_SSL'),
        wwwRedirect: (int) env('WWW_REDIRECT')
    )
    ->register(function (RouteCreator $router) {
        $router->load(__DIR__ . '/front.route.php');
        $router->load(__DIR__ . '/admin.route.php');

        $router->load(__DIR__ . '/packages/*.route.php');
    });
