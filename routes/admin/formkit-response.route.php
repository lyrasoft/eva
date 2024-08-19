<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Admin\FormkitResponse\FormkitResponseController;
use App\Module\Admin\FormkitResponse\FormkitResponseEditView;
use App\Module\Admin\FormkitResponse\FormkitResponseListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('formkit-response')
    ->extra('menu', ['sidemenu' => 'formkit_response_list'])
    ->register(function (RouteCreator $router) {
        $router->any('formkit_response_list', '/formkit-response/list')
            ->controller(FormkitResponseController::class)
            ->view(FormkitResponseListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('formkit_response_edit', '/formkit-response/edit[/{id}]')
            ->controller(FormkitResponseController::class)
            ->view(FormkitResponseEditView::class);
    });
