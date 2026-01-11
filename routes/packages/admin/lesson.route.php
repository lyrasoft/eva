<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Lesson\LessonController;
use Lyrasoft\Melo\Module\Admin\Lesson\LessonEditView;
use Lyrasoft\Melo\Module\Admin\Lesson\LessonFileController;
use Lyrasoft\Melo\Module\Admin\Lesson\LessonListView;
use Lyrasoft\Melo\Module\Admin\Segment\SegmentController;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('lesson')
    ->extra('menu', ['sidemenu' => 'lesson_list'])
    ->register(function (RouteCreator $router) {
        $router->any('lesson_list', '/lesson/list')
            ->controller(LessonController::class)
            ->view(LessonListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('lesson_edit', '/lesson/edit[/{id}]')
            ->controller(LessonController::class)
            ->view(LessonEditView::class);

        $router->any('ajax_segment', '/ajax/segment[/{task}]')
            ->controller(SegmentController::class, 'ajax');

        $router->any('lesson_file', '/lesson/file[/{task}]')
            ->controller(LessonFileController::class, 'index');

        $router->group('lesson_scope')
            ->middleware(
                KeepUrlQueryMiddleware::di(options: ['key' => 'lesson_id'])
            )
            ->prefix('lesson/{lesson_id}')
            ->register(function (RouteCreator $router) {
                $router->load(__DIR__ . '/lesson/*.php');
            });
    });
