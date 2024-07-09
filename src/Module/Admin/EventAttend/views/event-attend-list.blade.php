<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventAttendListView The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Event;
use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Enum\AttendState;
use App\Workflow\EventAttendStateWorkflow;
use Unicorn\Workflow\BasicStateWorkflow;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use App\Module\Admin\EventAttend\EventAttendListView;

/**
 * @var $item       EventAttend
 * @var $inStage    bool
 * @var $event      Event
 * @var $eventStage EventStage
 */

$workflow = $app->service(EventAttendStateWorkflow::class);
?>

@extends(
    $inStage
        ? 'layout.event.event-stage-edit-layout'
        : 'admin.global.body-list'
)

@section('toolbar-buttons')
    @include('list-toolbar')
@stop

@section($inStage ? 'card-content' : 'content')
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
                        <x-sort field="event_attend.state">
                            @lang('unicorn.field.state')
                        </x-sort>
                    </th>

                    @if (!$inStage)
                        {{-- Event --}}
                        <th>
                            活動
                        </th>
                    @endif

                    {{-- Name --}}
                    <th class="text-nowrap">
                        姓名
                    </th>

                    {{-- Order No --}}
                    <th class="text-nowrap">
                        <x-sort field="order.no">
                            訂單編號
                        </x-sort>
                    </th>

                    {{-- Plan --}}
                    <th class="text-nowrap">
                        <x-sort field="event_attend.plan_id">
                            方案
                        </x-sort>
                    </th>

                    {{-- Contact --}}
                    <th class="text-nowrap">
                        聯絡方式
                    </th>

                    {{-- Alternate --}}
                    <th class="text-nowrap">
                        候補
                    </th>

                    <th>
                        簽到
                    </th>

                    {{-- Delete --}}
                    {{--<th style="width: 1%" class="text-nowrap">--}}
                    {{--    @lang('unicorn.field.delete')--}}
                    {{--</th>--}}

                    {{-- ID --}}
                    <th style="width: 1%" class="text-nowrap text-end">
                        <x-sort field="event_attend.id">
                            @lang('unicorn.field.id')
                        </x-sort>
                    </th>
                </tr>
                </thead>

                <tbody>
                @forelse($items as $i => $item)
                    @php
                        $order = $vm->tryEntity(EventOrder::class, $item->order);
                        $plan = $vm->tryEntity(EventPlan::class, $item->plan);
                        $event = $vm->tryEntity(Event::class, $item->event);
                        $stage = $vm->tryEntity(EventStage::class, $item->stage);
                    @endphp
                    <tr>
                        {{-- Checkbox --}}
                        <td>
                            <x-row-checkbox :row="$i" :id="$item->getId()"></x-row-checkbox>
                        </td>

                        {{-- State --}}
                        <td class="text-nowrap">
                            <x-state-dropdown color-on="text"
                                button-style="width: 100%"
                                :workflow="$workflow"
                                :id="$item->getId()"
                                :value="$item->state"
                            ></x-state-dropdown>
                        </td>

                        @if (!$inStage)
                            {{-- Event --}}
                            <td>
                                <div>
                                    {{ $event->getTitle() }}
                                </div>
                                <div class="text-muted small mt-1">
                                    {{ $stage->getTitle() }}
                                </div>
                            </td>
                        @endif

                        {{-- Name --}}
                        <td>
                            <div>
                                <a href="{{ $nav->to('event_attend_edit')->id($item->getId()) }}">
                                    {{ $item->getName() }}
                                </a>
                            </div>

                            <div class="small text-secondary">
                                {{ $item->getNick() }}
                            </div>
                        </td>

                        {{-- Order No --}}
                        <td>
                            #{{ $order->getNo() }}
                        </td>

                        {{-- Plan --}}
                        <td>
                            {{ $item->getPlanTitle() }}
                        </td>

                        {{-- Contact --}}
                        <td>
                            @if ($item->getEmail())
                                <div class="mb-1">
                                    <i class="far fa-fw fa-envelope"></i>
                                    {{ $item->getEmail() }}
                                </div>
                            @endif
                            @if ($item->getMobile())
                                <div class="mb-1">
                                    <i class="far fa-fw fa-mobile"></i>
                                    {{ $item->getMobile() }}
                                </div>
                            @endif

                            @if ($item->getPhone())
                                <div>
                                    <i class="far fa-fw fa-phone"></i>
                                    {{ $item->getPhone() }}
                                </div>
                            @endif
                        </td>

                        {{-- Alt --}}
                        <td>
                            @if ($item->isAlternate())
                                <i class="far fa-check"></i>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if ($item->getState() === AttendState::BOOKED)
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    style="width: 100px"
                                    @click="$store.grid.updateItem('{{ $item->getId()}}', null, { batch: { 'state': 'checked_in' } })"
                                >
                                    <i class="far fa-sign-in"></i>
                                    簽到
                                </button>
                            @elseif($item->getState() === AttendState::CHECKED_IN)
                                <button type="button" class="btn btn-sm btn-success"
                                    style="width: 100px"
                                    disabled>
                                    <i class="far fa-check"></i>
                                    已簽到
                                </button>
                            @endif
                        </td>

                        {{--                        --}}{{-- Delete --}}
                        {{--                        <td class="text-center">--}}
                        {{--                            <button type="button" class="btn btn-sm btn-outline-secondary"--}}
                        {{--                                @click="grid.deleteItem('{{ $item->getId() }}')"--}}
                        {{--                                data-dos--}}
                        {{--                            >--}}
                        {{--                                <i class="fa-solid fa-trash"></i>--}}
                        {{--                            </button>--}}
                        {{--                        </td>--}}

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

@stop
