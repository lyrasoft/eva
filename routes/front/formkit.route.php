<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\Formkit\FormkitController;
use App\Module\Front\Formkit\FormkitItemView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('formkit')
    ->extra('menu', ['sidemenu' => 'formkit_list'])
    ->register(function (RouteCreator $router) {
        $router->any('formkit_item', '/form/{alias}')
            ->controller(FormkitController::class)
            ->view(FormkitItemView::class);
    });