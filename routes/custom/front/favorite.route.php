<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Favorite\FavoritePackage;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */
$router->load(FavoritePackage::path('routes/front/*.php'));
