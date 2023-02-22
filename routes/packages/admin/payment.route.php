<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Payment\PaymentController;
use Lyrasoft\ShopGo\Module\Admin\Payment\PaymentEditView;
use Lyrasoft\ShopGo\Module\Admin\Payment\PaymentListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('payment')
    ->extra('menu', ['sidemenu' => 'payment_list'])
    ->register(function (RouteCreator $router) {
        $router->any('payment_list', '/payment/list')
            ->controller(PaymentController::class)
            ->view(PaymentListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('payment_edit', '/payment/edit[/{id}]')
            ->controller(PaymentController::class)
            ->view(PaymentEditView::class);
    });
