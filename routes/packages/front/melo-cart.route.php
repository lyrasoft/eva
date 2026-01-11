<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Front\LessonCart\LessonCartController;
use Lyrasoft\Melo\Module\Front\LessonCart\LessonCartView;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->any('melo_cart', 'lesson/cart')
    ->controller(LessonCartController::class)
    ->view(LessonCartView::class);

$router->any('melo_cart_ajax', 'lesson/cart/ajax[/{task}]')
    ->controller(LessonCartController::class, 'ajax');

$router->any('melo_checkout', 'lesson/checkout')
    ->controller(LessonCartController::class)
    ->postHandler('checkout');
