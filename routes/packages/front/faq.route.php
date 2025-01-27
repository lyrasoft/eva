<?php

namespace App\Routes;

use Lyrasoft\Faq\Module\Front\Faq\FaqListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('faq')
    ->register(function (RouteCreator $router) {
        $router->any('faq_list', '/faq/list')
            ->view(FaqListView::class);
            //->controller()
            //->view();
    });
