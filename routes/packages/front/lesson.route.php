<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Lesson\LessonController;
use Lyrasoft\Melo\Module\Front\Lesson\LessonItemView;
use Lyrasoft\Melo\Module\Front\Lesson\LessonListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('lesson')
    ->register(function (RouteCreator $router) {
        $router->any('lesson_list', '/lesson/list')
            ->view(LessonListView::class);

        $router->any('lesson_item', '/lesson/{id:\d+}[-{alias}[/attend/{segment_id}]]')
            ->view(LessonItemView::class);

        $router->any('lesson_ajax', '/lesson/ajax[/{task}]')
            ->controller(LessonController::class, 'ajax');
    });
