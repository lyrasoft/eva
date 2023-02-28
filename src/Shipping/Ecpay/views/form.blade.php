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

use App\Shipping\Ecpay\EcpayShipping;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

use function Windwalker\uid;

/**
 * @var $shipping EcpayShipping
 */
$uid = uid();

$mapRoute = $nav->to('shipping_task')
    ->task('mapSelect')
    ->var('callback', $callbackName = 'mapSelected_' . $uid)
    ->id($shipping->getData()->getId());

$id = 'shipping-form-' . $uid;


?>

<div id="{{ $id }}" class="l-shipping-form d-flex gap-3 align-items-center">
    <div class="l-shipping-form__button">
        <button type="button" class="btn btn-outline-primary" style="width: 135px"
            data-task="map-select"
            data-url="{{ $mapRoute }}"
            onclick="mapSelect(this)">
            選擇門市
        </button>
    </div>

    <div class="l-shipping-form__info">
        <span class="text-danger">請選擇送貨門市</span>
        <input type="text" value="" class="d-none" required data-validation-message="請先選擇送貨門市" />
    </div>
</div>

<script>
  window.mapSelect = function (button) {
    window.open(button.dataset.url, '', `width=900, height=600, top=300, left=300`);

    window.{{ $callbackName }} = function (data) {
      const wrapper = document.querySelector('#{{$id}} .l-shipping-form__info');

      const html = `
    <div class="d-flex gap-2 align-items-center">
        <h6 class="m-0">${data.CVSStoreName}</h6>
        <p class="text-muted m-0">${data.CVSAddress}</p>

        <input name="checkout[shipping][CVSAddress]" type="hidden" value="${data.CVSAddress}" />
        <input name="checkout[shipping][CVSOutSide]" type="hidden" value="${data.CVSOutSide}" />
        <input name="checkout[shipping][CVSStoreID]" type="hidden" value="${data.CVSStoreID}" />
        <input name="checkout[shipping][CVSStoreName]" type="hidden" value="${data.CVSStoreName}" />
        <input name="checkout[shipping][CVSTelephone]" type="hidden" value="${data.CVSTelephone}" />
        <input name="checkout[shipping][LogisticsSubType]" type="hidden" value="${data.LogisticsSubType}" />
    </div>
    `;

      wrapper.innerHTML = html;
    };
  }
</script>


