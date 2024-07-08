<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventPlanListView The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\EventPlan;
use Unicorn\Workflow\BasicStateWorkflow;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use App\Module\Admin\EventPlan\EventPlanListView;

use function EventBooking\priceFormat;

/**
 * @var $item EventPlan
 */

$workflow = $app->service(BasicStateWorkflow::class);
?>

@extends('layout.event.event-stage-edit-layout')

@section('toolbar-buttons')
    @include('list-toolbar')
@stop

@section('edit-content')
    <x-card>
        <form id="admin-form" action="" x-data="{ grid: $store.grid }"
            x-ref="gridForm"
            data-ordering="{{ $ordering }}"
            method="post">

            <x-filter-bar :form="$form" :open="$showFilters"></x-filter-bar>

            {{-- RESPONSIVE TABLE DESC --}}
            <div class="d-block d-lg-none mb-3">
                @lang('unicorn.grid.responsive.table.desc')
            </div>

            <div class="grid-table table-responsive-lg">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        {{-- Toggle --}}
                        <th style="width: 1%">
                            <x-toggle-all></x-toggle-all>
                        </th>

                        {{-- State --}}
                        <th style="width: 5%" class="text-nowrap">
                            <x-sort field="event_plan.state">
                                @lang('unicorn.field.state')
                            </x-sort>
                        </th>

                        {{-- Title --}}
                        <th class="text-nowrap">
                            <x-sort field="event_plan.title">
                                @lang('unicorn.field.title')
                            </x-sort>
                        </th>

                        {{-- Price --}}
                        <th class="text-end">
                            <x-sort field="event_plan.price">
                                票價
                            </x-sort>
                        </th>

                        {{-- Quota / Sold --}}
                        <th class="text-end">
                            <x-sort field="event_plan.sold">
                                人數
                            </x-sort>
                        </th>

                        {{-- Sale --}}
                        <th>
                            銷售中
                        </th>

                        {{-- Start --}}
                        <th>
                            <x-sort field="event_plan.start_date">
                                開始
                            </x-sort>
                        </th>

                        {{-- End --}}
                        <th>
                            <x-sort field="event_plan.end_date">
                                結束
                            </x-sort>
                        </th>

                        {{-- Delete --}}
                        <th style="width: 1%" class="text-nowrap">
                            @lang('unicorn.field.delete')
                        </th>

                        {{-- ID --}}
                        <th style="width: 1%" class="text-nowrap text-end">
                            <x-sort field="event_plan.id">
                                @lang('unicorn.field.id')
                            </x-sort>
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            {{-- Checkbox --}}
                            <td>
                                <x-row-checkbox :row="$i" :id="$item->getId()"></x-row-checkbox>
                            </td>

                            {{-- State --}}
                            <td>
                                <x-state-dropdown color-on="text"
                                    button-style="width: 100%"
                                    use-states
                                    :workflow="$workflow"
                                    :id="$item->getId()"
                                    :value="$item->state"
                                ></x-state-dropdown>
                            </td>

                            {{-- Title --}}
                            <td>
                                <div>
                                    <a href="{{ $nav->to('event_plan_edit')->id($item->getId()) }}">
                                        {{ $item->getTitle() }}
                                    </a>

                                    @if ($item->isRequireValidate())
                                        <i class="far fa-shield-check"
                                            data-bs-toggle="tooltip"
                                            title="需要審核"
                                        ></i>
                                    @endif
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="text-end">
                                {{ $vm->priceFormat($item->getPrice()) }}
                            </td>

                            {{-- Sold / Quota --}}
                            <td class="text-end">
                                {{ priceFormat($item->getSold()) }}
                                /
                                {{ priceFormat($item->getQuota()) }}
                            </td>

                            <td>
                                @if ($item->getState()->isUnpublished())
                                    <div class="text-danger">
                                        <i class="far fa-pause"></i>
                                        關閉
                                    </div>
                                @else
                                    @if (!$item->getStartDate() || $item->getStartDate()->isPast())
                                        @if (!$item->getEndDate() || $item->getEndDate()->isFuture())
                                            <div class="text-success">
                                                <i class="far fa-play"></i>
                                                銷售中
                                            </div>
                                        @else
                                            <div class="text-danger">
                                                <i class="far fa-stop"></i>
                                                已結束
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-warning">
                                            <i class="far fa-clock"></i>
                                            尚未開始
                                        </div>
                                    @endif
                                @endif
                            </td>

                            {{-- Start --}}
                            <td>
                                {{ $chronos->toLocalFormat($item->getStartDate(), 'Y-m-d H:i') ?: '-' }}
                            </td>

                            {{-- End --}}
                            <td>
                                {{ $chronos->toLocalFormat($item->getEndDate(), 'Y-m-d H:i') ?: '-' }}
                            </td>

                            {{-- Delete --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    @click="grid.deleteItem('{{ $item->getId() }}')"
                                    data-dos
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>

                            {{-- ID --}}
                            <td class="text-end">
                                {{ $item->getId() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="30">
                                <div class="c-grid-no-items text-center" style="padding: 125px 0;">
                                    <h3 class="text-secondary">@lang('unicorn.grid.no.items')</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div>
                    <x-pagination :pagination="$pagination"></x-pagination>
                </div>
            </div>

            <div class="d-none">
                <input name="_method" type="hidden" value="PUT" />
                <x-csrf></x-csrf>
            </div>

            <x-batch-modal :form="$form" namespace="batch"></x-batch-modal>
        </form>
    </x-card>

@stop
