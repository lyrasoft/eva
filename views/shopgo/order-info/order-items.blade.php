<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use Lyrasoft\ShopGo\Cart\Price\PriceSet;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Service\CurrencyService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $orderItems OrderItem[]
 * @var $totals     PriceSet
 */

$currency = $app->service(CurrencyService::class);
?>

<div class="l-order-items card mb-5">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 5%">
                @lang('unicorn.field.id')
            </th>
            <th style="width: 5%">
                @lang('shopgo.order.item.field.image')
            </th>
            <th>
                @lang('shopgo.order.item.field.product')
            </th>
            <th>
                @lang('shopgo.order.item.field.quantity')
            </th>
            <th>
                @lang('shopgo.order.item.field.total')
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($orderItems as $orderItem)
            <tr>
                <td>{{ $orderItem->getId() }}</td>
                <td>
                    <img src="{{ $orderItem->getImage() }}" class="me-3" width="100"
                        alt="Image">
                </td>
                <td>
                    <h4>
                        {{ $orderItem->getTitle() }}
                    </h4>
                    @if ($orderItem->getVariantHash())
                        <div>
                            {{ $orderItem->getVariantTitle() }}
                        </div>
                    @endif
                </td>
                <td style="width: 200px">{{ $orderItem->getQuantity() }}</td>
                <td class="text-end" style="width: 200px">
                    {{ $currency->format($orderItem->getTotal()) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-end mb-5">
        <table class="table">
            <tr data-type="{{ $totals['total']->getParamValue('code') }}"
                data-discount-id="{{ $totals['total']->getParamValue('discountId') }}">
                <th style="border: none">
                    <div>
                        {{ $totals['total']->getLabel() }}
                    </div>
                </th>
                <td style="border: none; vertical-align: middle; width: 150px;">
                    {{ $currency->format($totals['total']) }}
                </td>
            </tr>

            @foreach ($totals as $total)
                @if (!str_starts_with($total->getName(), 'discount:'))
                    @continue
                @endif

                <tr data-type="{{ $total->getParamValue('code') }}"
                    data-discount-id="{{ $total->getParamValue('discountId') }}">
                    <th style="border: none">
                        <div>
                            {{ $total->getLabel() }}
                        </div>
                    </th>
                    <td style="border: none; vertical-align: middle" width="150px">
                        {{ $currency->format($total) }}
                    </td>
                </tr>
            @endforeach

            @if (!$totals['shipping_fee']->isZero())
                <tr data-type="{{ $totals['shipping_fee']->getParamValue('code') }}"
                    data-discount-id="{{ $totals['shipping_fee']->getParamValue('discountId') }}">
                    <th style="border: none">
                        <div>
                            {{ $totals['shipping_fee']->getLabel() }}
                        </div>
                    </th>
                    <td style="border: none; vertical-align: middle" width="150px">
                        {{ $currency->format($totals['shipping_fee']) }}
                    </td>
                </tr>
            @endif

            @if ($totals->has('free_shipping'))
                @if(!$totals['free_shipping']->isZero())
                    <tr data-type="{{ $totals['free_shipping']->getParamValue('code') }}"
                        data-discount-id="{{ $totals['free_shipping']->getParamValue('discountId') }}">
                        <th style="border: none">
                            <div>
                                {{ $totals['free_shipping']->getLabel() }}
                            </div>
                        </th>
                        <td style="border: none; vertical-align: middle; width: 150px;">
                            {{ $currency->format($totals['free_shipping']) }}
                        </td>
                    </tr>
                @endif
            @endif

            <tr data-type="{{ $totals['grand_total']->getParamValue('code') }}"
                data-discount-id="{{ $totals['grand_total']->getParamValue('discountId') }}">
                <th style="border: none">
                    <div>
                        {{ $totals['grand_total']->getLabel() }}
                    </div>
                </th>
                <td style="border: none; vertical-align: middle; width: 150px;">
                    {{ $currency->format($totals['grand_total']) }}
                </td>
            </tr>
        </table>

    </div>
</div>
