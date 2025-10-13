<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Middleware\LoginRequireMiddleware;
use Unicorn\Module\FileUpload\FileController;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->post('file_upload', '/file/upload')
    ->controller(FileController::class, 'upload')
    ->middleware(LoginRequireMiddleware::class)
    ->middleware(JsonApiMiddleware::class);
