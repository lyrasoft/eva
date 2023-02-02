<?php

declare(strict_types=1);

namespace App\View;

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
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var Order $order
 */

?>

<div class="card">
    <div class="card-body">
        <dl class="row p-2 mb-0">
            <dt class="col-4">
                @lang('shopgo.order.field.no')
            </dt>
            <dd class="col-8">
                #{{ $order->getNo() }}
            </dd>
            <dt class="col-4">
                @lang('shopgo.order.field.state')
            </dt>
            <dd class="col-8">
                @php($state = $order->getState())
                <span class="badge px-2 py-1"
                    style="font-size: .875rem; {{ $state->getColorCSS() }}">
                    {{ $order->getState()->getTitle() }}
                </span>
            </dd>
            <dt class="col-4">
                @lang('shopgo.order.field.created')
            </dt>
            <dd class="col-8">
                {{ $chronos->toLocalFormat($order->getCreated()) }}
            </dd>
            <dt class="col-4">
                @lang('shopgo.order.field.paid.at')
            </dt>
            <dd class="col-8">
                @if ($order->getPaidAt())
                    {{ $chronos->toLocalFormat($order->getPaidAt()) }}
                @else
                    -
                @endif
            </dd>
        </dl>
    </div>
</div>
