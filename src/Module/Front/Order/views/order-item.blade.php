<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        OrderItemView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Module\Front\Order\OrderItemView;
use Lyrasoft\ShopGo\Cart\Price\PriceSet;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var Order       $item
 * @var OrderItem[] $orderItems
 * @var PriceSet    $totals
 */
?>

@extends('global.body')

@section('content')
    <div class="container l-order-item my-5">
        <form name="admin-form" id="admin-form"
            uni-form-validate='{"scroll": true}'
            action="{{ $nav->to('order_item') }}"
            method="POST" enctype="multipart/form-data">

            <div class="d-flex flex-column gap-4">
                <div class="row">
                    <div class="col-md-4">
                        <x-order-info.col1 :order="$item"></x-order-info.col1>
                    </div>
                    <div class="col-md-4">
                        <x-order-info.col2 :order="$item"></x-order-info.col2>
                    </div>
                    <div class="col-md-4">
                        <x-order-info.col3 :order="$item"></x-order-info.col3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-order-info.payment-data :order="$item"></x-order-info.payment-data>
                    </div>
                    <div class="col-md-6">
                        <x-order-info.shipping-data :order="$item"></x-order-info.shipping-data>
                    </div>
                </div>

                {{-- Order Items--}}
                <x-order-info.order-items
                    :order="$item"
                    :order-items="$orderItems"
                    :totals="$totals"
                ></x-order-info.order-items>
            </div>

            <div class="">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            @lang('shopgo.order.field.histories')
                        </div>
                    </div>

                    <x-order-histories :histories="$histories" class="list-group-flush"></x-order-histories>
                </div>
            </div>

            <div class="d-none">
                <input name="no" type="hidden" value="{{ $item->getNo() }}" />
                <x-csrf></x-csrf>
            </div>
        </form>
    </div>
@stop
