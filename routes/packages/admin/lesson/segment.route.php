<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Melo\Module\Admin\Segment\SegmentController;
use Lyrasoft\Melo\Module\Admin\Segment\SegmentEditView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('segment')
    ->extra('menu', ['sidemenu' => 'segment_list'])
    ->register(function (RouteCreator $router) {
        $router->any('segment_edit', '/segment/edit')
            ->controller(SegmentController::class)
            ->view(SegmentEditView::class);
    });
