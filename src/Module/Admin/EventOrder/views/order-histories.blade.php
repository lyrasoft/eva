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

use App\Data\EventOrderHistory;
use App\Enum\OrderHistoryType;
use App\Service\EventOrderStateService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

/**
 * @var EventOrderHistory   $history
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
        <div class="list-group-item c-order-history">
            <div class="c-order-history__info d-flex text-muted mb-2">
                <div class="c-order-history__info-item mr-2">
                    <span class="fa fa-calendar"></span>
                    {{ $chronos->toLocalFormat($history->getCreated()) }}
                </div>

                @if ($history->isNotify())
                    <div class="c-order-history__info-item ms-2">
                    <span class="fa fa-envelope"
                        data-bs-toggle="tooltip"
                        title="@lang('event.order.history.action.notify')"></span>
                    </div>
                @endif
            </div>
            <div class="c-order-history__title">
                {{ $history->getType()->trans($lang) }}

                @if ($history->getType() !== OrderHistoryType::SYSTEM && $history->getUserId())
                    <a href="{{ $nav->to('user_edit', ['id' => $history->getUserId() ?: 0]) }}">
                        {{ $history->getUserName() }}
                    </a>
                @endif

                @if ($history->getState())
                    @lang('event.order.history.action.changed.to')
                    <span class="badge bg-{{ $history->getState()->getColor() }}">
                    {{ $history->getStateText() }}
                </span>
                @endif

                @if (trim($history->getMessage()) !== '')
                    @if ($history->getState())
                        @lang('event.order.history.action.and.comments')
                    @else
                        @lang('event.order.history.action.comments')
                    @endif
                @endif
            </div>

            @if (trim($history->getMessage()) !== '')
                <div class="c-order-history__message p-2 bg-light mt-2">
                    {!! html_escape($history->getMessage(), true) !!}
                </div>
            @endif
        </div>
    @endforeach
</div>
