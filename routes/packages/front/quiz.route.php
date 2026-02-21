<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Quiz\QuizController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('quiz')
    ->register(function (RouteCreator $router) {
        $router->any('quiz_ajax', '/quiz/ajax[/{task}]')
            ->controller(QuizController::class, 'ajax');
    });
