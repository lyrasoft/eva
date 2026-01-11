<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Question\QuestionController;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('question')
    ->extra('menu', ['sidemenu' => 'question_list'])
    ->register(function (RouteCreator $router) {
        $router->any('ajax_question', '/ajax/question[/{task}]')
            ->controller(QuestionController::class, 'ajax');
    });
