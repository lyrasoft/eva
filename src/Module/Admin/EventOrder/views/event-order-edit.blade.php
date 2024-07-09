<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventOrderEditView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Data\EventOrderTotal;
use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use App\Module\Admin\EventOrder\EventOrderEditView;
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
 */

$priceFormatter = $app->retrieve(PriceFormatter::class);

$alert = $item->getParams()['alert'] ?? [];

$screenshots = $item->getScreenshots();

$event = $vm->tryEntity(Event::class, $screenshots['event']);
$stage = $vm->tryEntity(Event::class, $screenshots['stage']);
?>

@extends('admin.global.body-edit')

@section('toolbar-buttons')
    @include('edit-toolbar')
@stop

@section('content')
    <form name="admin-form" id="admin-form"
        uni-form-validate='{"scroll": true}'
        action="{{ $nav->to('event_order_edit') }}"
        method="POST" enctype="multipart/form-data">

        <div class="d-flex flex-column gap-4">
            @if ($alert)
                @foreach ($alert as $msg)
                    <div class="alert alert-warning">
                        {{ $msg }}
                    </div>
                @endforeach
            @endif

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
        </div>

        <div class="d-none">
            @if ($idField = $form?->getField('item/id') ?? $form?->getField('id'))
                <input name="{{ $idField->getInputName() }}" type="hidden" value="{{ $idField->getValue() }}" />
            @endif

            <x-csrf></x-csrf>
        </div>
    </form>
@stop
