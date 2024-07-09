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

use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;

/**
 * @var $item       EventOrder
 * @var $event      Event
 * @var $stage      EventStage
 * @var $attributes ComponentAttributes
 */

$attributes->props(
    'item',
    'event',
    'stage'
);

?>
<x-card class="l-order-info__col1">
    <table class="c-order-info-table">
        <tr class="">
            <th style="width: 30%">訂單編號</th>
            <td>
                <strong>#{{ $item->getNo() }}</strong>
            </td>
        </tr>

        <tr class="">
            <th>狀態</th>
            <td>{{ $item->getState()->getTitle($lang) }}</td>
        </tr>

        <tr class="">
            <th>活動</th>
            <td>{{ $event->getTitle() }}</td>
        </tr>

        <tr class="">
            <th>梯次</th>
            <td>{{ $stage->getTitle() }}</td>
        </tr>

        <tr class="">
            <th>建立時間</th>
            <td>{{ $chronos->toLocalFormat($item->getCreated(), 'Y-m-d H:i') }}</td>
        </tr>

        {{--<tr class="">--}}
        {{--    <th>候補</th>--}}
        {{--    <td>{{ $item->getAlternates() > 0 ? '是' : '否' }}</td>--}}
        {{--</tr>--}}
    </table>
</x-card>
