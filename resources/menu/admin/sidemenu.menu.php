<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Menu;

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

$menu->header('MENU');

$menu->link('用戶', '#')
    ->icon('fal fa-users-gear');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // User
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.user.title')))
            ->to($nav->to('user_list'))
            ->icon('fal fa-users');
    }
);

$menu->link('內容管理', '#')
    ->icon('fal fa-pen-ruler');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Category
        $menu->link($lang('luna.article.category.list'))
            ->to($nav->to('category_list', ['type' => 'article']))
            ->icon('fal fa-sitemap');

        // Article
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.article.title')))
            ->to($nav->to('article_list'))
            ->icon('fal fa-newspaper');

        // Page
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.page.title')))
            ->to($nav->to('page_list'))
            ->icon('fal fa-files');

        // Tag
        $menu->link($lang('unicorn.title.grid', title: $lang('luna.tag.title')))
            ->to($nav->to('tag_list'))
            ->icon('fal fa-tags');
    }
);

// Portfolio
$menu->link('案例作品', '#')
    ->icon('fal fa-photo-film');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Category
        $menu->link('作品分類')
            ->to($nav->to('category_list', ['type' => 'portfolio']))
            ->icon('fal fa-sitemap');

        // Portfolio
        $menu->link('作品管理')
            ->to($nav->to('portfolio_list'))
            ->icon('fal fa-images');
    }
);

// Portfolio
$menu->link('團隊成員', '#')
    ->icon('fal fa-people-pants-simple');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Category
        $menu->link('團隊分類')
            ->to($nav->to('category_list', ['type' => 'member']))
            ->icon('fal fa-sitemap');

        // Portfolio
        $menu->link('成員管理')
            ->to($nav->to('member_list'))
            ->icon('fal fa-person');
    }
);

// Menu
$menu->link($lang('luna.menu.manager.title', title: $lang('luna.menu.type.mainmenu')))
    ->to($nav->to('menu_list', ['type' => 'mainmenu']))
    ->icon('fal fa-list');

// Configs
$menu->link('設定檔', '#')
    ->icon('fal fa-cogs');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        // Config Core
        $menu->link($lang('luna.config.title', $lang('luna.config.type.core')))
            ->to($nav->to('config_core'))
            ->icon('fal fa-cog');
    }
);
