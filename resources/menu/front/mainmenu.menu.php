<?php

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

$menu->link('首頁', $nav->to('home'))
    ->icon('fal fa-home');

$menu->link('文章', $nav->to('article_category'))
    ->icon('fal fa-files');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $categories = $app->make(CategoryRepository::class)
            ->getListSelector()
            ->where('category.state', 1)
            ->where('category.type', 'article')
            ->ordering('category.lft', 'ASC');

        $categoryNodes = TreeBuilder::create($categories);

        $menu->fromTree(
            $categoryNodes,
            function (NodeInterface $node, MenuBuilder $menu) use ($nav) {
                $menu->link(
                    $node->getValue()->title,
                    $nav->to('article_category')->var('path', $node->getValue()->path)
                );
            }
        );
    }
);

$menu->link('功能', '#');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $menu->link('團隊成員', '#');

        $menu->registerChildren(
            function (MenuBuilder $menu) use ($app, $nav) {
                $categories = $app->make(CategoryRepository::class)
                    ->getAvailableListSelector()
                    ->where('category.type', 'member')
                    ->ordering('category.lft', 'ASC');

                foreach ($categories as $category) {
                    $menu->link(
                        $category->title,
                        $nav->to('member_category')->var('path', $category->path)
                    );
                }
            }
        );

        $menu->link('作品', '#');

        $menu->registerChildren(
            function (MenuBuilder $menu) use ($app, $nav) {
                $categories = $app->make(CategoryRepository::class)
                    ->getAvailableListSelector()
                    ->where('category.type', 'portfolio')
                    ->ordering('category.lft', 'ASC');

                foreach ($categories as $category) {
                    $menu->link(
                        $category->title,
                        $nav->to('portfolio_category')->var('path', $category->path)
                    );
                }
            }
        );
    }
);

$menu->link('活動', $nav->to('event_stage_list'))
    ->icon('fal fa-calendar');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $menu->link('活動列表', $nav->to('event_stage_list'));
        $menu->link('我的活動', $nav->to('my_event_list'));
        $menu->link('我的活動訂單', $nav->to('event_order_list'));
    }
);

$menu->link('商城', $nav->to('product_list'))
    ->icon('fal fa-shopping-cart');

$menu->link('課程', $nav->to('lesson_list'))
    ->icon('fal fa-calendar');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($app, $nav) {
        $menu->link('課程總覽', $nav->to('lesson_list'));
        $menu->link('我的課程訂單', $nav->to('melo_order_list'));
    }
);
