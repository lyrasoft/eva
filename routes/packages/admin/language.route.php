<?php

namespace App\Routes;

use Lyrasoft\Luna\Module\Admin\Language\LanguageController;
use Lyrasoft\Luna\Module\Admin\Language\LanguageEditView;
use Lyrasoft\Luna\Module\Admin\Language\LanguageListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('language')
    ->extra('menu', ['sidemenu' => 'language_list'])
    ->register(function (RouteCreator $router) {
        $router->any('language_list', '/language/list')
            ->controller(LanguageController::class)
            ->view(LanguageListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('language_edit', '/language/edit[/{id}]')
            ->controller(LanguageController::class)
            ->view(LanguageEditView::class);
    });
