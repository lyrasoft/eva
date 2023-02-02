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

use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Service\LocationService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $order Order
 */

$shippingData = $order->getShippingData();
$locationService = $app->service(LocationService::class);

$dtCols = 3;
$ddCols = 12 - $dtCols;
?>

<div class="l-payment-data card">
    <div class="card-header">
        @lang('shopgo.order.shipping.data.title')
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            {{-- Name --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.address.field.name')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                {{ $shippingData->getFullName() }}
            </dd>

            {{-- Email --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.address.field.email')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                {{ $shippingData->getEmail() ?: '-' }}
            </dd>

            {{-- Phone --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.address.field.phone')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                {{ $shippingData->getPhone() ?: '-' }}
            </dd>

            {{-- Mobile --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.address.field.mobile')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                {{ $shippingData->getMobile() ?: '-' }}
            </dd>

            {{-- Address --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.address.field.full.address')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                @php($address = $locationService->formatAddress($shippingData))

                @if ($address)
                    <a href="https://google.com/maps?q={{ $address }}" target="_blank">
                        {{ $address }}
                        <i class="fa fa-external-link small"></i>
                    </a>
                @else
                    -
                @endif
            </dd>

            {{-- Note --}}
            <dt class="col-lg-{{ $dtCols }}">
                @lang('shopgo.order.field.shipping.note')
            </dt>
            <dd class="col-lg-{{ $ddCols }}">
                {{ $shippingData->getNote() ?: '-' }}
            </dd>
        </dl>
    </div>
</div>
