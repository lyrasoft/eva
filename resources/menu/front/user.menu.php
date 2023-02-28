<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Menu;

use Lyrasoft\Luna\Repository\CategoryRepository;
use Lyrasoft\Luna\Tree\NodeInterface;
use Lyrasoft\Luna\Tree\TreeBuilder;
use Windwalker\Core\Application\AppContext;
use Lyrasoft\Luna\Menu\MenuBuilder;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Language\LangService;

/**
 * @var MenuBuilder $menu
 * @var AppContext $app
 * @var Navigator $nav
 * @var LangService $lang
 */

$menu->link('會員選單', '#')
    ->icon('fal fa-user');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $menu->link('我的訂單', $nav->to('my_order_list'))
            ->icon('fa fa-fw fa-file');

        $menu->link('待買清單', $nav->to('wishlist'))
            ->icon('fa fa-fw fa-heart');
    }
);
