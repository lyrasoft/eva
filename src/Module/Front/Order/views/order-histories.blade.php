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

use Lyrasoft\ShopGo\Entity\OrderHistory;
use Lyrasoft\ShopGo\Enum\OrderHistoryType;
use Lyrasoft\ShopGo\Service\OrderStateService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

/**
 * @var OrderHistory        $history
 * @var ComponentAttributes $attributes
 */

$attributes->props(
    'histories',
    'order',
);

$attributes = $attributes->class('list-group');
?>

{!! $slot ?? '' !!}

<div {!! $attributes !!}>
    @foreach($histories as $history)
        <div class="list-group-item order-history">
            <div class="order-history__info d-flex text-muted mb-2">
                <div class="order-history__info-item mr-2">
                    <span class="fa fa-calendar"></span>
                    {{ $chronos->toLocalFormat($history->getCreated()) }}
                </div>

                @if ($history->isNotify())
                    <div class="order-history__info-item ms-2">
                    <span class="fa fa-envelope"
                        data-bs-toggle="tooltip"
                        title="@lang('shopgo.order.history.action.notify')"></span>
                    </div>
                @endif
            </div>
            <div class="order-history__title">
                {{ $history->getType()->trans($lang) }}

                @if ($history->getType() !== OrderHistoryType::SYSTEM())
                    <a href="{{ $nav->to('user_edit', ['id' => $history->getCreatedBy()]) }}">
                        {{ $history->user->name }}
                    </a>
                @endif

                @if ($history->getStateId())
                    @lang('shopgo.order.history.action.changed.to')
                    <span class="badge"
                        style="{{ OrderStateService::colorToCSS($history->getStateColor()) }}">
                    {{ $history->getStateText() }}
                </span>
                @endif

                @if (trim($history->getMessage()) !== '')
                    @if ($history->getStateId())
                        @lang('shopgo.order.history.action.and.comments')
                    @else
                        @lang('shopgo.order.history.action.comments')
                    @endif
                @endif
            </div>

            @if (trim($history->getMessage()) !== '')
                <div class="order-history__message p-2 bg-light mt-2">
                    {!! html_escape($history->getMessage(), true) !!}
                </div>
            @endif
        </div>
    @endforeach
</div>
