<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Formkit\Module\Front\Formkit\FormkitController;
use Lyrasoft\Formkit\Module\Front\Formkit\FormkitItemView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('formkit')
    ->extra('menu', ['sidemenu' => 'formkit_list'])
    ->register(function (RouteCreator $router) {
        $router->any('formkit_item', '/form/{alias}')
            ->controller(FormkitController::class)
            ->view(FormkitItemView::class);

        $router->post('formkit_submit', '/form/{id}/submit')
            ->controller(FormkitController::class, 'submit');
    });
