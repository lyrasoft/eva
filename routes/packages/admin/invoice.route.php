<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Invoice\InvoiceController;
use Lyrasoft\ShopGo\Module\Admin\Invoice\InvoiceView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('invoice')
    ->register(function (RouteCreator $router) {
        $router->any('invoice', '/invoice')
            ->controller(InvoiceController::class)
            ->postHandler('create')
            ->view(InvoiceView::class);
    });
