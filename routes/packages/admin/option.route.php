<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Option\OptionController;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('option')
    ->extra('menu', ['sidemenu' => 'option_list'])
    ->register(function (RouteCreator $router) {
        $router->any('ajax_option', '/ajax/option')
            ->controller(OptionController::class, 'ajax');
    });
