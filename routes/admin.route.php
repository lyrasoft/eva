<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use App\Module\Admin\AdminMiddleware;
use Lyrasoft\Contact\ContactPackage;
use Lyrasoft\Firewall\FirewallPackage;
use Lyrasoft\Formkit\FormkitPackage;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Middleware\AdminAccessMiddleware;
use Lyrasoft\Luna\Middleware\LoginRequireMiddleware;
use Windwalker\Core\Middleware\CsrfMiddleware;
use Windwalker\Core\Middleware\MetadataMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('admin')
    ->prefix('/admin')
    ->namespace('admin')
    ->middleware(CsrfMiddleware::class)
    ->middleware(MetadataMiddleware::class, meta: ['robots' => 'noindex'])
    ->middleware(
        LoginRequireMiddleware::class,
        excludes: [
            'admin::login',
            'admin::logout',
            'admin::auth_ajax',
        ]
    )
    ->middleware(AdminAccessMiddleware::class)
    ->middleware(AdminMiddleware::class)
    ->register(function (RouteCreator $router) {
        $router->load(__DIR__ . '/admin/*.php');

        $router->load(__DIR__ . '/packages/admin/*.route.php');

        $router->load(__DIR__ . '/custom/admin/*.route.php');

        $router->load(LunaPackage::path('routes/admin/*.route.php'));
        $router->load(FirewallPackage::path('routes/admin/*.route.php'));
        $router->load(ContactPackage::path('routes/admin/*.route.php'));
        $router->load(FormkitPackage::path('routes/admin/*.route.php'));
    });
