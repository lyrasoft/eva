<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Investor\Module\Admin\ShareholderInfo\ShareholderInfoController;
use Lyrasoft\Investor\Module\Admin\ShareholderInfo\ShareholderInfoEditView;
use Lyrasoft\Investor\Module\Admin\ShareholderInfo\ShareholderInfoListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('shareholder-info')
    ->extra('menu', ['sidemenu' => 'shareholder_info_list'])
    ->register(function (RouteCreator $router) {
        $router->any('shareholder_info_list', '/shareholder-info/list')
            ->controller(ShareholderInfoController::class)
            ->view(ShareholderInfoListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('shareholder_info_edit', '/shareholder-info/edit[/{id}]')
            ->controller(ShareholderInfoController::class)
            ->view(ShareholderInfoEditView::class);
    });
