<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Student\StudentController;
use Lyrasoft\Melo\Module\Admin\Student\StudentLessonListView;
use Lyrasoft\Melo\Module\Admin\Student\StudentListView;
use Lyrasoft\Melo\Module\Admin\UserHomework\UserHomeworkController;
use Lyrasoft\Melo\Module\Admin\UserHomework\UserHomeworkListView;
use Lyrasoft\Melo\Module\Admin\UserQuiz\UserQuizController;
use Lyrasoft\Melo\Module\Admin\UserQuiz\UserQuizListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('student')
    ->register(function (RouteCreator $router) {
        $router->any('user_quiz_list', '/student/quiz/list')
            ->extra('menu', ['sidemenu' => 'user_quiz_list'])
            ->controller(UserQuizController::class)
            ->view(UserQuizListView::class)
            ->putHandler('filter')
            ->postHandler('copy')
            ->patchHandler('batch');

        $router->any('user_homework_list', '/student/homework/list')
            ->extra('menu', ['sidemenu' => 'user_homework_list'])
            ->controller(UserHomeworkController::class)
            ->view(UserHomeworkListView::class)
            ->putHandler('filter')
            ->postHandler('copy')
            ->patchHandler('batch');

        $router->any('student_list', '/student/list')
            ->extra('menu', ['sidemenu' => 'student_list'])
            ->controller(StudentController::class)
            ->view(StudentListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->group('student_scope')
            ->middleware(
                KeepUrlQueryMiddleware::di(options: ['key' => 'user_id'])
            )
            ->prefix('student/{user_id}')
            ->register(function (RouteCreator $router) {
                $router->any('student_lesson_list', '/lessons')
                    ->extra('menu', ['sidemenu' => 'student_lesson_list'])
                    ->controller(StudentController::class)
                    ->view(StudentLessonListView::class)
                    ->postHandler('copy')
                    ->putHandler('filter')
                    ->patchHandler('batch');
            });
    });
