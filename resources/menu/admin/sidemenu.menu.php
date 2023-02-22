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

$menu->link('商城管理', '#')
    ->icon('fal fa-shop');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        $menu->link($lang('shopgo.product.category.title'))
            ->to($nav->to('category_list', ['type' => 'product']))
            ->icon('fal fa-sitemap');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.product.title')))
            ->to($nav->to('product_list'))
            ->icon('fal fa-box-open');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.additional.purchase.title')))
            ->to($nav->to('additional_purchase_list'))
            ->icon('fal fa-cart-plus');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.product.feature.title')))
            ->to($nav->to('product_feature_list'))
            ->icon('fal fa-object-ungroup');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.product.attribute.group.title')))
            ->to($nav->to('product_attribute_group_list'))
            ->icon('fal fa-object-group');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.product.attribute.title')))
            ->to($nav->to('product_attribute_list'))
            ->icon('fal fa-rectangle-list');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.product.tab.title')))
            ->to($nav->to('product_tab_list'))
            ->icon('fal fa-pager');

        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.manufacturer.title')))
            ->to($nav->to('manufacturer_list'))
            ->icon('fal fa-building');
    }
);

$menu->link('優惠', '#')
    ->icon('fal fa-cart-arrow-down');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.discount.title')))
            ->to($nav->to('discount_list'))
            ->icon('fal fa-percent');
    }
);

$menu->link('訂單', '#')
    ->icon('fal fa-file-invoice-dollar');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.order.title')))
            ->to($nav->to('order_list'))
            ->icon('fal fa-file-invoice-dollar');

        $menu->link($lang('unicorn.title.grid', title: $lang('luna.order_state.title')))
            ->to($nav->to('order_state_list'))
            ->icon('fal fa-list');
    }
);

$menu->link('商城設定', '#')
    ->icon('fal fa-cogs');

$menu->registerChildren(
    function (MenuBuilder $menu) use ($nav, $lang) {
        $menu->link($lang('unicorn.title.grid', title: $lang('shopgo.currency.title')))
            ->to($nav->to('currency_list'))
            ->icon('fal fa-sterling-sign');

        $menu->link($lang('unicorn.title.grid', title: $lang('luna.location.title')))
            ->to($nav->to('location_list'))
            ->icon('fa-solid fa-marker')
            ->icon('fal fa-earth-asia');

        $menu->link($lang('unicorn.title.grid', title: $lang('luna.payment.title')))
            ->to($nav->to('payment_list'))
            ->icon('fa-solid fa-dollar')
            ->icon('fal fa-cash-register');

        $menu->link($lang('unicorn.title.grid', title: $lang('luna.shipping.title')))
            ->to($nav->to('shipping_list'))
            ->icon('fal fa-truck');

        $menu->link($lang('luna.config.title', $lang('shopgo.config.type.shop')))
            ->to($nav->to('config_shopgo_shop'))
            ->icon('fal fa-gear');
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

// Banner
$menu->link('橫幅管理')
    ->to($nav->to('banner_list'))
    ->icon('fal fa-gallery-thumbnails');

// Widget
$menu->link($lang('unicorn.title.grid', title: $lang('luna.widget.title')))
    ->to($nav->to('widget_list'))
    ->icon('fal fa-shapes');

// Menu
$menu->link($lang('luna.menu.manager.title', title: $lang('luna.menu.type.mainmenu')))
    ->to($nav->to('menu_list', ['type' => 'mainmenu']))
    ->icon('fal fa-list');

// Contact
$menu->link($lang('contact.list.title', title: $lang('contact.main.title')))
    ->to($nav->to('contact_list', ['type' => 'main']))
    ->icon('fal fa-phone-volume');

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
