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

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Entity\EventPlan;
use App\Service\PriceFormatter;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Edge\Component\ComponentAttributes;
use Windwalker\ORM\ORM;

/**
 * @var $item        EventOrder
 * @var $attends     EventAttend[]
 * @var $attend      EventAttend
 * @var $plan        EventPlan
 * @var $attributes  ComponentAttributes
 */

$attributes->props(
    'item',
    'attends'
);

$orm = $app->retrieve(ORM::class);
$priceFormatter = $app->retrieve(PriceFormatter::class);
?>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-end">ID</th>
        <th>編號</th>
        <th>狀態</th>
        <th>姓名</th>
        <th>方案</th>
        <th>聯絡方式</th>
        {{--<th>候補</th>--}}
        <th class="text-end">價格</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($attends as $attend)
        @php
            $plan = $orm->tryEntity(EventPlan::class, $attend->plan);
        @endphp

        <tr>
            <td class="text-end">
                {{ $attend->getId() }}
            </td>
            <td>
                #{{ $attend->getNo() }}
            </td>
            <td>
                {{ $attend->getState()->getTitle($lang) }}
            </td>
            <td>
                <div>
                    {{ $attend->getName() }}
                </div>

                @if ($attend->getNick())
                    <div class="small text-muted mt-1">
                        {{ $attend->getNick() }}
                    </div>
                @endif
            </td>
            <td>
                {{ $attend->getPlanTitle() }}
            </td>
            <td>
                @if ($attend->getEmail())
                    <div class="mb-1">
                        <i class="far fa-fw fa-envelope"></i>
                        {{ $attend->getEmail() }}
                    </div>
                @endif
                @if ($attend->getMobile())
                    <div class="mb-1">
                        <i class="far fa-fw fa-mobile"></i>
                        {{ $attend->getMobile() }}
                    </div>
                @endif
                @if ($attend->getPhone())
                    <div>
                        <i class="far fa-fw fa-phone"></i>
                        {{ $attend->getPhone() }}
                    </div>
                @endif
            </td>
            {{--<td>--}}
            {{--    ---}}
            {{--</td>--}}
            <td class="text-end">
                {{ $priceFormatter->format($attend->getPrice()) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
