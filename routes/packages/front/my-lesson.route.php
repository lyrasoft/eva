<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\MyLecture\MyLectureListView;
use Lyrasoft\Melo\Module\Front\MyLesson\MyLessonListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('my-lesson')
    ->prefix('/my')
    ->register(function (RouteCreator $router) {
        $router->get('my_lesson_list', '/lesson/list')
            ->view(MyLessonListView::class);

        $router->get('my_lecture_list', '/lecture/list')
            ->view(MyLectureListView::class);
    });
