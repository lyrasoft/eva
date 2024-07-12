<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        \App\Module\Front\EventOrder\\App\Module\Front\EventOrder\\App\Module\Front\EventOrder\\App\Module\Front\EventOrder\\App\Module\Front\EventOrder\\App\Module\Front\EventOrder\\App\Module\Front\EventStage\\App\Module\Front\EventOrder\\App\Module\Front\EventOrder\MyEventListView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use App\Entity\Venue;
use App\Module\Front\EventOrder\MyEventListView;
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
        <h2>我的活動</h2>

        <div>
            @foreach ($items as $item)
                @php
                    $event = $vm->tryEntity(Event::class, $item->event);
                    $stage = $vm->tryEntity(EventStage::class, $item->stage);
                    $venue = $vm->tryEntity(Venue::class, $item->venue);
                    $link = $nav->to('my_event_item')->var('no', $item->getNo())
                @endphp
                <x-card class="mb-4">
                    <div class="mb-2">
                        <h4 class="card-title">
                            <a href="{{ $link }}">
                                {{ $event?->getTitle() }} | {{ $stage?->getTitle() }}
                            </a>
                        </h4>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div>
                            開始: {{ $chronos->toLocalFormat($stage->getStartDate(), 'Y/m/d H:i') ?: '無' }}
                        </div>

                        <div>
                            結束: {{ $chronos->toLocalFormat($stage->getEndDate(), 'Y/m/d H:i') ?: '無' }}
                        </div>

                        <div>
                            <i class="far fa-user"></i>
                            {{ $item->getAttends() }}
                        </div>
                    </div>

                    <div class="mt-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            @if ($venue)
                                <div>
                                    <i class="far fa-house-flag"></i>
                                    場地: {{ $venue->getTitle() }}
                                </div>
                            @endif
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
