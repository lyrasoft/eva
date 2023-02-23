<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        \App\Module\Front\Checkout\CheckoutView          The view model object.
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

?>

@extends('global.body')

@section('content')
    <div class="container my-5">
        <form id="payment-form" action="{{ $nav->to('checkout_payment') }}" method="post">
            {!! $content !!}

            <div class="d-none">
                @foreach ($vm->flattenAsInputName($data, 'checkout') as $key => $value)
                    <input name="{{ $key }}" type="hidden" value="{{ $value }}" />
                @endforeach
            </div>
        </form>
    </div>
@stop
