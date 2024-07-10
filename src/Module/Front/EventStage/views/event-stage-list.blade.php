<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventStageListView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventStage;
use App\Module\Front\EventStage\EventStageListView;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Pagination\Pagination;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

use function Windwalker\str;

/**
 * @var $item       EventStage
 * @var $category   Category
 * @var $pagination Pagination
 */

?>

@extends('global.body')

@section('content')
    <div class="container my-4">
        <div>
            @foreach ($items as $item)
                @php
                    $event = $vm->tryEntity(Event::class, $item->event);
                    $category = $vm->tryEntity(Category::class, $item->category);
                @endphp
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-title">
                            <a href="{{ $item->makeLink($nav) }}">
                                <h4>
                                    {{ $event->getTitle() }} | {{ $item->getTitle() }}
                                </h4>
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if ($item->getStartDate())
                                <div>
                                    開始: {{ $chronos->toLocalFormat($item->getStartDate(), 'Y/m/d H:i') }}
                                </div>
                            @endif

                            @if ($item->getEndDate())
                                <div>
                                    結束: {{ $chronos->toLocalFormat($item->getEndDate(), 'Y/m/d H:i') }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div>
                                <i class="far fa-user"></i>
                                {{ $item->getQuota() }}
                            </div>

                            @if ($category)
                                <div>
                                    <a href="{{ $nav->to('event_stage_list')->var('path', $category->getPath()) }}"
                                        class="link-secondary">
                                        <i class="far fa-folder"></i>
                                        {{ $category->getTitle() }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div>
                            {!! str($item->getDescription())->stripHtmlTags()->truncate(150, '...') !!}
                        </div>

                        <div class="mt-3">
                            <a href="{{ $item->makeLink($nav) }}"
                                class="btn btn-outline-primary"
                                style="width: 150px">
                                前往觀看
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            <x-pagination :pagination="$pagination"></x-pagination>
        </div>
    </div>
@stop
