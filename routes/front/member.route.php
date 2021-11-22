<?php

namespace App\Routes;

use App\Module\Front\Member\MemberItemView;
use App\Module\Front\Member\MemberListView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('portfolio')
    ->register(function (RouteCreator $router) {
        $router->get('member_category', '/members[/{path:.+}]')
            ->view(MemberListView::class);

        $router->get('member_item', '/member/{id:\d+}-{alias}')
            ->view(MemberItemView::class);
    });
