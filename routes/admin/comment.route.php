<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Admin\Comment\CommentController;
use App\Module\Admin\Comment\CommentEditView;
use App\Module\Admin\Comment\CommentListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('comment')
    ->extra('menu', ['sidemenu' => 'comment_list'])
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'type',
            'uid' => 'comment_type',
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('comment_list', '/comment/list/{type}')
            ->controller(CommentController::class)
            ->view(CommentListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('comment_edit', '/comment/edit/{type}[/{id}]')
            ->controller(CommentController::class)
            ->view(CommentEditView::class);
    });
