<?php

namespace App\Routes;

use App\Module\Admin\Member\MemberController;
use App\Module\Admin\Member\MemberEditView;
use App\Module\Admin\Member\MemberListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('member')
    ->register(function (RouteCreator $router) {
        $router->any('member_list', '/member/list')
            ->controller(MemberController::class)
            ->view(MemberListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('member_edit', '/member/edit[/{id}]')
            ->controller(MemberController::class)
            ->view(MemberEditView::class);
    });
