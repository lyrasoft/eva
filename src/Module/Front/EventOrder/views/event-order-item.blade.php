<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventOrderItemView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Data\EventOrderHistory;
use App\Data\EventOrderTotal;
use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use App\Module\Front\EventOrder\EventOrderItemView;
use App\Service\PriceFormatter;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Form\Form;

/**
 * @var $form       Form
 * @var $item       EventOrder
 * @var $event      Event
 * @var $eventStage EventStage
 * @var $total      EventOrderTotal
 * @var $history    EventOrderHistory
 */

$priceFormatter = $app->retrieve(PriceFormatter::class);

$screenshots = $item->getScreenshots();

$event = $vm->tryEntity(Event::class, $screenshots['event']);
$stage = $vm->tryEntity(EventStage::class, $screenshots['stage']);

$histories = $item->getHistories();
?>

@extends('global.body')

@section('content')
    <div class="container my-4">

        <form id="order-form" action="" method="post">
            <div class="d-flex flex-column gap-4">
                {{-- Order Info --}}
                <div class="row l-order-info">
                    {{-- Col 1 --}}
                    <div class="col-lg-4">
                        <x-order-info.col1 :item="$item" :event="$event" :stage="$stage"></x-order-info.col1>
                    </div>

                    {{-- Col 2 --}}
                    <div class="col-lg-4">
                        <x-order-info.col2 :item="$item"></x-order-info.col2>
                    </div>

                    {{-- Col 3 --}}
                    <div class="col-lg-4">
                        <x-order-info.col3 :item="$item"></x-order-info.col3>
                    </div>
                </div>

                @if ($paymentInfo)
                    <x-card header="付款資訊">
                        {!! $paymentInfo !!}
                    </x-card>
                @endif

                {{-- Atendees --}}
                <div class="card">
                    <h4 class="card-header">
                        參與人員
                    </h4>

                    <x-order-info.attends :item="$item" :attends="$attends"></x-order-info.attends>

                    <div class="card-body">
                        @php
                            $totals = $item->getTotals();
                        @endphp

                        @foreach ($totals as $total)
                            <div class="d-flex justify-content-end align-items-center">
                                <div>
                                    <strong>{{ $total->getTitle() }}</strong>
                                </div>
                                <div class="text-end fs-4 fw-bold" style="min-width: 150px">
                                    {{ $priceFormatter->format($total->getValue()) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Histories --}}
                <div class="">
                    <h4>訂單歷史</h4>

                    <table class="table table-striped">
                        <tbody>
                        @foreach ($histories as $history)
                        <tr>
                            <td>
                                {{ $history->getStateText() }}
                            </td>
                            <td>
                                {{ $history->getType()->getTitle($lang) }}
                            </td>
                            <td>
                                {!! html_escape($history->getMessage(), true) !!}
                            </td>
                            <td>
                                {{ $chronos->toLocalFormat($history->getCreated(), 'Y/m/d H:i') }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

    </div>
@stop
