<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Admin\Formkit\FormkitController;
use App\Module\Admin\Formkit\FormkitEditView;
use App\Module\Admin\Formkit\FormkitListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('formkit')
    ->extra('menu', ['sidemenu' => 'formkit_list'])
    ->register(function (RouteCreator $router) {
        $router->any('formkit_list', '/formkit/list')
            ->controller(FormkitController::class)
            ->view(FormkitListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('formkit_edit', '/formkit/edit[/{id}]')
            ->controller(FormkitController::class)
            ->view(FormkitEditView::class);
    });
