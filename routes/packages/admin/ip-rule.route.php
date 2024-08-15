<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Firewall\Module\Admin\IpRule\IpRuleController;
use Lyrasoft\Firewall\Module\Admin\IpRule\IpRuleEditView;
use Lyrasoft\Firewall\Module\Admin\IpRule\IpRuleListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('ip-rule')
    ->extra('menu', ['sidemenu' => 'ip_rule_list'])
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'type',
            'uid' => 'ip_rule_type'
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('ip_rule_list', '/ip-rule/list/{type}')
            ->controller(IpRuleController::class)
            ->view(IpRuleListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('ip_rule_edit', '/ip-rule/edit/{type}[/{id}]')
            ->controller(IpRuleController::class)
            ->view(IpRuleEditView::class);
    });
