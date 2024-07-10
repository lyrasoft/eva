<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventStageItemView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Module\Front\EventStage\EventStageItemView;
use App\Service\PriceFormatter;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $item      EventStage
 * @var $event     Event
 * @var $category  Category
 * @var $plan      EventPlan
 */

$priceFormatter = $app->retrieve(PriceFormatter::class);
?>

@extends('global.body')

@section('content')
    <div class="container my-4 l-event-stage">

        <section class="l-event-stage-body mt-5">
            <div class="mx-auto d-flex flex-column gap-4" style="max-width: 800px;">
                <div class="text-center">
                    <img src="{{ $item->getCover() }}" class="img-fluid" style="max-width: 800px" alt="cover">
                </div>

                <header class="l-event-stage-header">
                    <h2>{{ $event->getTitle() }} | {{ $item->getTitle() }}</h2>
                </header>

                <aside>
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
                </aside>

                <div>
                    {!! $event->getDescription() !!}
                </div>

                <div>
                    {!! $item->getDescription() !!}
                </div>

                @php
                $canAttend = false;
                @endphp

                <form id="attend-form" action="{{ $nav->to('event_attending') }}" method="post">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>方案</th>
                            <th>狀態</th>
                            <th class="text-end">價格</th>
                            <th class="text-end" style="width: 30%">報名</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($plans as $plan)
                            @php
                            $sale = true;

                            if ($plan->getStartDate() && $plan->getStartDate()->isFuture()) {
                                $sale = false;
                            }

                            if ($plan->getEndDate() && $plan->getEndDate()->isPast()) {
                                $sale = false;
                            }

                            $canAttend = $canAttend || $sale;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fs-5">{{ $plan->getTitle() }}</div>
                                </td>
                                <td>
                                    @if ($sale)
                                        <div>
                                            <i class="far fa-fw fa-play"></i>
                                            銷售中
                                        </div>
                                    @else
                                        @if ($plan->getStartDate() && $plan->getStartDate()->isFuture())
                                            <div>
                                                <i class="far fa-fw fa-clock"></i>
                                                尚未開賣
                                            </div>
                                        @elseif ($plan->getEndDate() && $plan->getEndDate()->isPast())
                                            <div>
                                                <i class="far fa-fw fa-stop"></i>
                                                結束販售
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        @if ($plan->getOriginPrice())
                                            <del class="text-muted">
                                                {{ $priceFormatter->format($plan->getOriginPrice()) }}
                                            </del>
                                        @endif

                                        <div class="fs-5">
                                            @if (!$plan->getPrice())
                                                免費
                                            @else
                                                {{ $priceFormatter->format($plan->getPrice()) }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    @if ($sale)
                                        <select name="quantity[{{ $plan->getId() }}]"
                                            id="input-quantity-{{ $plan->getId() }}"
                                            class="form-select ms-auto"
                                        >
                                            <option value="">- 選擇人數 -</option>
                                            @foreach (range(1, $plan->getOnceMax()) as $n)
                                                <option value="{{ $n }}">
                                                    {{ $n }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10" class="text-end">
                                <button type="submit" class="btn btn-primary"
                                    data-dos
                                    style="min-width: 150px"
                                    @attr('disabled', !$canAttend)
                                >
                                    報名
                                </button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="d-none">
                        <input name="stage_id" type="hidden" value="{{ $item->getId() }}" />

                        <x-csrf></x-csrf>
                    </div>
                </form>
            </div>
        </section>
    </div>
@stop
