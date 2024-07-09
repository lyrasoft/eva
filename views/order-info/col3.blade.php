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
 * @var $item        EventOrder
 * @var $attributes  ComponentAttributes
 */

$attributes->props(
    'item',
);

?>
<x-card class="l-order-info__col3">
    <table class="c-order-info-table">
        <tr class="">
            <th style="width: 30%">付款方式</th>
            <td>
                {{ $item->getPayment() }}
            </td>
        </tr>

        <tr class="">
            <th>交易編號</th>
            <td>{{ $item->getTransactionNo() }}</td>
        </tr>

        <tr class="">
            <th>發票類型</th>
            <td>{{ $item->getInvoiceType()->getTitle($lang) }}</td>
        </tr>

        <tr class="">
            <th>發票編號</th>
            <td>{{ $item->getInvoiceData()->getNo() }}</td>
        </tr>

        <tr class="">
            <th>付款日期</th>
            <td>{{ $chronos->toLocalFormat($item->getPaidAt(), 'Y-m-d H:i') ?: '-' }}</td>
        </tr>
    </table>
</x-card>
