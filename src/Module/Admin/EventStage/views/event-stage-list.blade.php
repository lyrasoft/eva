<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventStageListView The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\EventStage;
use Unicorn\Workflow\BasicStateWorkflow;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use App\Module\Admin\EventStage\EventStageListView;

use function EventBooking\priceFormat;

/**
 * @var $item EventStage
 */

$workflow = $app->service(BasicStateWorkflow::class);
?>

@extends('admin.global.body-edit')

@section('toolbar-buttons')
    @include('list-toolbar')
@stop

@section('content')
    <x-event-edit-nav :event-id="$event->getId()"></x-event-edit-nav>

    <div class="card">
        <div class="card-body">
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
                                <x-sort field="event_stage.state">
                                    @lang('unicorn.field.state')
                                </x-sort>
                            </th>

                            {{-- Title --}}
                            <th class="text-nowrap">
                                <x-sort field="event_stage.title">
                                    @lang('unicorn.field.title')
                                </x-sort>
                            </th>

                            {{-- Venue --}}
                            <th class="text-nowrap">
                                <x-sort field="event_stage.venue_id">
                                    場地
                                </x-sort>
                            </th>

                            {{-- Start --}}
                            <th class="text-nowrap">
                                <x-sort field="event_stage.start_date">
                                    開始時間
                                </x-sort>
                            </th>

                            {{-- End --}}
                            <th class="text-nowrap">
                                <x-sort field="event_stage.end_date">
                                    結束時間
                                </x-sort>
                            </th>

                            {{-- Attends / Quota --}}
                            <th class="text-nowrap text-end">
                                <x-sort field="event_stage.attends">
                                    人數
                                </x-sort>
                            </th>

                            {{-- Less --}}
                            <th class="text-nowrap text-end">
                                <x-sort field="event_stage.less">
                                    最低人數
                                </x-sort>
                            </th>

                            {{-- Alt --}}
                            <th class="text-nowrap text-end">
                                <x-sort field="event_stage.alternate">
                                    可候補
                                </x-sort>
                            </th>

                            {{-- Delete --}}
                            <th style="width: 1%" class="text-nowrap">
                                @lang('unicorn.field.delete')
                            </th>

                            {{-- ID --}}
                            <th style="width: 1%" class="text-nowrap text-end">
                                <x-sort field="event_stage.id">
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
                                        <a href="{{ $nav->to('event_stage_edit')->id($item->getId()) }}">
                                            {{ $item->getTitle() }}
                                        </a>
                                    </div>
                                </td>

                                {{-- Venue --}}
                                <td>
                                    @if ($item->venue)
                                        <i class="far fa-house-flag"></i>
                                        {{ $item->venue?->title }}
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

                                {{-- Attends / Quota --}}
                                <td class="text-end">
                                    {{ priceFormat($item->getAttends()) }}
                                    /
                                    {{ priceFormat($item->getQuota()) }}
                                </td>

                                {{-- Less --}}
                                <td class="text-end">
                                    {{ priceFormat($item->getLess()) ?: '-' }}
                                </td>

                                {{-- Alt --}}
                                <td class="text-end">
                                    {{ priceFormat($item->getAlternate()) ?: '-' }}
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
        </div>
    </div>

@stop
