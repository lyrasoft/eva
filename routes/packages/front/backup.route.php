<?php

namespace App\Routes;

use Lyrasoft\Backup\Module\Backup\BackupController;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('backup')
    ->register(function (RouteCreator $router) {
        $router->any('backup', '/backup')
            ->controller(BackupController::class, 'backup');
    });
