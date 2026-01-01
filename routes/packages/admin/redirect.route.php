<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Firewall\Module\Admin\Redirect\RedirectController;
use Lyrasoft\Firewall\Module\Admin\Redirect\RedirectEditView;
use Lyrasoft\Firewall\Module\Admin\Redirect\RedirectListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('redirect')
    ->extra('menu', ['sidemenu' => 'redirect_list'])
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'type',
            'uid' => 'redirect_type'
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('redirect_list', '/redirect/list/{type}')
            ->controller(RedirectController::class)
            ->view(RedirectListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('redirect_edit', '/redirect/edit/{type}[/{id}]')
            ->controller(RedirectController::class)
            ->view(RedirectEditView::class);
    });
