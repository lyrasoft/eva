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

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Script\ShopGoScript;
use Lyrasoft\ShopGo\Service\LocationService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Image\ImagePlaceholder;
use Unicorn\Script\UnicornScript;
use Unicorn\Script\VueScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$shopGo = $app->service(ShopGoPackage::class);
$imagePlaceholder = $app->service(ImagePlaceholder::class);
$userService = $app->service(UserService::class);

$shopGoScript = $app->service(ShopGoScript::class);
$shopGoScript->vueUtilities();
$shopGoScript->productCart();

$locationService = $app->service(LocationService::class);

$uniScript = $app->service(UnicornScript::class);
$uniScript->data('cart.props', [
    'user' => $userService->isLogin() ? $userService->getUser() : null,
    'checkoutData' => $checkoutData ?: new \stdClass(),
]);
$uniScript->data('image.default', $imagePlaceholder->placeholderSquare());
$uniScript->data('location.labels', $locationService->getSelectorLabels());
$uniScript->data('partial.checkout', (bool) $shopGo->config('checkout.partial_checkout'));

$uniScript->addRoute('@home');
$uniScript->addRoute('@cart_ajax');
$uniScript->addRoute('@address_ajax');

$uniScript->translate('unicorn.select.placeholder');
$uniScript->translate('shopgo.cart.*');
$uniScript->translate('shopgo.address.*');
$uniScript->translate('shopgo.message.*');
?>

@extends('global.body')

@section('content')
    <div class="l-cart-page container my-5">
        <form id="cart-form" ref="form" action="{{ $nav->to('checkout_shipping') }}" method="post"
            style="--sidebar-offsets-top: 90px; --sidebar-offsets-bottom: 30px">
            <cart-app></cart-app>
            <div class="d-none">
                <x-csrf></x-csrf>
            </div>
        </form>
    </div>
@stop
