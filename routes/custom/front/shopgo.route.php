<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */
$router->load(ShopGoPackage::path('routes/front/*.php'));
