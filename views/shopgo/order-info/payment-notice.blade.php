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

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var \Lyrasoft\ShopGo\Entity\Order $order
 */
?>

@if ($order->getPayment()->equals(\App\Enum\OrderPayment::ATM()))
    <p>
        您的繳款方式為 {{ $order->getPayment()->trans($lang) }}。
    </p>

        <?php
        $expiry = \Windwalker\chronos('+6days');
        ?>

    <p>
        提醒您，請在 7 日內完成付款，屆時訂單將自動取消無法付款。若有任何問題請聯繫客服。
    </p>
@endif
