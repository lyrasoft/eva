<?php

namespace App\Routes;

use Lyrasoft\Faq\Module\Admin\Faq\FaqController;
use Lyrasoft\Faq\Module\Admin\Faq\FaqEditView;
use Lyrasoft\Faq\Module\Admin\Faq\FaqListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('faq')
    ->register(function (RouteCreator $router) {
        $router->any('faq_list', '/faq/list')
            ->controller(FaqController::class)
            ->view(FaqListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('faq_edit', '/faq/edit[/{id}]')
            ->controller(FaqController::class)
            ->view(FaqEditView::class);
    });
