<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        OrderListView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Module\Front\Order\OrderListView;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderState;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\ORM\ORM;

/**
 * @var $entity Order
 * @var $state ?OrderState
 */
$orm = $app->service(ORM::class);
?>

@extends('global.body')

@section('content')
    <div class="container l-order-list my-5">

        <h2>
            我的訂單
        </h2>

        <div class="l-order-items">

            @foreach ($items as $item)
                <?php
                    $entity = $vm->prepareItem($item);

                    if ($item->order_state?->id) {
                        $state = $orm->toEntity(OrderState::class, $item->order_state);
                    } else {
                        $state = null;
                    }
                ?>

                <div class="c-order-item card my-3">
                    <div class="card-body ">
                        <div class="d-lg-flex d-grid gap-3 justify-content-between">
                            <div class="d-flex align-items-center gap-3 ">
                                <div class="gs-5 fw-bold">#{{ $entity->getNo() }}</div>
                                <span class="badge px-3 py-2 w-100"
                                    style="{{ $state?->getColorCSS() ?: 'background: var(--bs-dark)' }} font-size: .9375rem;">
                                {{ $state?->getTitle() ?: $entity->getStateText() }}
                            </span>
                            </div>


                        </div>

                        <div class="mt-3">
                            <div class="d-flex gap-3">
                                <div>
                                    下單日期: {{ $chronos->toLocalFormat($entity->getCreated(), 'Y/m/d') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-lg-flex d-grid gap-3 justify-content-between mt-3">
                            <div class=" " >
                                <span class="">訂單金額: </span>
                                <span class="fs-5 fw-bold">{{ $vm->formatPrice($entity->getTotal(), true) }}</span>
                            </div>

                            <div>
                                <a href="{{ $nav->to('order_item')->var('no', $entity->getNo()) }}"
                                    class="btn btn-secondary stretched-link"
                                    style="min-width: 150px">
                                    訂單詳情
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach

        </div>

        <div class="mt-4">
            <x-pagination :pagination="$pagination"></x-pagination>
        </div>

    </div>
@stop
