<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\Module\Admin\Config\Form\ShopgoShopForm;
use Lyrasoft\Luna\Module\Admin\Config\ConfigController;
use Lyrasoft\Luna\Module\Admin\Config\ConfigEditView;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('shopgo-config')
    ->register(function (RouteCreator $router) {
        $router->any('config_shopgo_shop', '/config/shopgo-shop')
            ->controller(ConfigController::class)
            ->view(ConfigEditView::class)
            ->var('type', 'shopgo_shop')
            ->var('form', ShopgoShopForm::class)
            ->layoutPaths(
                ShopGoPackage::path('Module/Admin/Config/views')
            );
    });
