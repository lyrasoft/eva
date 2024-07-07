<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use App\Entity\EventStage;
use Unicorn\Legacy\Html\MenuHelper;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\ORM\ORM;

$menu = $app->service(MenuHelper::class);
$orm = $app->retrieve(ORM::class);
$eventStageId = $app->input('eventStageId');
$eventStage ??= null;

if (!$eventStage && $eventStageId) {
    $eventStage = $orm->mustFindOne(EventStage::class, $eventStageId);
}

$links = [
    [
        'title' => '梯次編輯',
        'link' => fn() => $nav->to('event_stage_edit')->id($eventStage->getId()),
        'active' => fn() => $menu->active('event_stage_edit')
    ],
    [
        'title' => '票種管理',
        'link' => fn() => $nav->to('event_plan_list')->var('eventStageId', $eventStage->getId()),
        'active' => fn() => $menu->active('event_plan_list')
    ],
    [
        'title' => '報名者管理',
        'link' => fn() => $nav->to('event_plan_list')->var('eventStageId', $eventStage->getId()),
        'active' => fn() => $menu->active('event_plan_list')
    ],
];
?>

@extends('admin.global.body-edit')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <nav class="nav nav-pills flex-column">
                <a class="nav-item nav-link bg-light mb-2"
                    href="{{ $nav->to('event_stage_list') }}">
                    <i class="far fa-chevron-left"></i>
                    回到梯次列表
                </a>

                @foreach ($links as $linkItem)
                    <a class="nav-item nav-link {{ $linkItem['active']() }} {{ $eventStage ? '' : 'disabled' }}"
                        href="{{ $eventStage ? $linkItem['link']() : '' }}">
                        {{ $linkItem['title'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="col-md-10">
            @yield('edit-content')
        </div>
    </div>
@stop
