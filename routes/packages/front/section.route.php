<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\Segment\SectionController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('section')
    ->register(function (RouteCreator $router) {
        $router->any('section_file', '/section/file/{id}')
            ->controller(SectionController::class, 'file');

        $router->any('section_ajax', '/section/ajax[/{task}]')
            ->controller(SectionController::class, 'ajax');
    });
