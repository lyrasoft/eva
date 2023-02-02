<?php

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

declare(strict_types=1);

namespace App\View;

use Lyrasoft\ShopGo\Data\ShippingHistory;
use Lyrasoft\ShopGo\Entity\Order;

/**
 * @var Order             $order
 * @var ShippingHistory[] $histories
 */

$histories = $order->getShippingHistory()->dump();

$histories = array_reverse($histories);
?>
<button type="button" class="btn btn-outline-dark text-nowrap"
    data-bs-toggle="modal"
    data-bs-target="#shipping-histories-{{ $order->getId() }}-modal"
>
    觀看貨運歷史 (限 dev 模式)
</button>

<div class="modal fade" id="shipping-histories-{{ $order->getId() }}-modal" tabindex="-1" role="dialog"
    aria-labelledby="shipping-histories-{{ $order->getId() }}-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="shipping-histories-{{ $order->getId() }}-modal-label">
                    貨運歷史 {{ $order->getNo() }}
                </h4>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true" class="visually-hidden">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul>
                    @foreach ($histories as $history)
                        <li>
                            {{ $chronos->toLocalFormat($history->getTime(), 'Y/m/d H:i') }}
                            -
                            {{ $history->getStatusText() }}
                            ({{ $history->getStatusCode() }})
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
