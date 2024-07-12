<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventOrderListView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use App\Module\Front\EventOrder\EventOrderListView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Pagination\Pagination;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $item       EventOrder
 * @var $pagination Pagination
 */

?>

@extends('global.body')

@section('content')
    <div class="container my-4">
        <h2>我的訂單</h2>

        <div>
            @foreach ($items as $item)
                @php
                    $event = $vm->tryEntity(Event::class, $item->event);
                    $stage = $vm->tryEntity(EventStage::class, $item->stage);
                    $link = $nav->to('event_order_item')->var('no', $item->getNo())
                @endphp
                <x-card class="mb-4">
                    <div class="card-title text-muted d-flex flex-items-center gap-3">
                        <a href="{{ $link }}">
                            #{{ $item->getNo() }}
                        </a>

                        <div>
                            <i class="far fa-clock"></i>
                            {{ $chronos->toLocalFormat($item->getCreated(), 'Y/m/d H:i') }}
                        </div>

                        <div>
                            <i class="far fa-user"></i>
                            {{ $item->getAttends() }}
                        </div>
                    </div>

                    <h4>
                        {{ $event?->getTitle() }} | {{ $stage?->getTitle() }}
                    </h4>

                    <div class="mt-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                            <span class="badge bg-{{ $item->getState()->getColor() }} fs-5">
                                {{ $item->getState()->getTitle($lang) }}
                            </span>
                            </div>
                        </div>

                        <div>
                            <a href="{{ $link }}" class="btn btn-primary" style="width: 150px">
                                觀看訂單
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <x-pagination :pagination="$pagination"></x-pagination>
    </div>
@stop
