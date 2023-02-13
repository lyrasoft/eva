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
use Unicorn\Image\ImagePlaceholder;
use Unicorn\Script\UnicornScript;
use Unicorn\Script\VueScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$imagePlaceholder = $app->service(ImagePlaceholder::class);
$userService = $app->service(UserService::class);

$shopGoScript = $app->service(ShopGoScript::class);
$shopGoScript->vueUtilities();
$shopGoScript->productCart();

$vueScript = $app->service(VueScript::class);
$vueScript->vue();
$vueScript->animate();

$uniScript = $app->service(UnicornScript::class);
$uniScript->data('cart.props', [
    'user' => $userService->isLogin() ? $userService->getUser() : null,
]);
$uniScript->data('addresses', $addresses);
$uniScript->data('image.default', $imagePlaceholder->placeholderSquare());

$uniScript->addRoute('@cart_ajax');
$uniScript->addRoute('@address_ajax');

?>

@extends('global.body')

@section('content')
    <div class="l-cart-page container my-5">
        <cart-app>
            <div class="row">
                <div class="col-lg-8 l-cart-page__content">
                    <header class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="m-0">購物車</h3>

                        <div>
                            <a href="javascript://">
                                <i class="fa fa-times"></i>
                                移除所有商品
                            </a>
                        </div>
                    </header>

                    <div class="l-cart-data">

                        {{-- Cart Items --}}
                        <div class="l-cart-items">

                            {{-- Cart Item --}}
                            <div class="c-cart-item card mb-3" v-for="item of items">
                                <div class="card-body d-grid d-lg-flex gap-3">
                                    <div class="d-flex gap-3">

                                        {{-- Cover --}}
                                        <div class="c-cart-item__image">
                                            <div style="width: 75px" class="ratio ratio-1x1">
                                                <img :src="item.cover" :alt="item.product.title"
                                                    style="">
                                            </div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="c-cart-item__content">
                                            <h5>@{{ item.product.title }}</h5>
                                            <div class="fs-6 text-muted">
                                                @{{ item.variant.title }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ms-auto d-flex align-items-center gap-3"
                                        style="max-width: 350px">

                                        {{-- Quantity --}}
                                        <div class="c-cart-item__quantity d-flex gap-2">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    @click="changeItemQuantity(item, -1)">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                <input type="text" class="form-control form-control-sm"
                                                    v-model.number="item.quantity"
                                                    @input="updateQuantities"
                                                />
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    @click="changeItemQuantity(item, +1)">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>

                                            {{-- Remove --}}
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>

                                        {{-- Item Total --}}
                                        <div class="c-cart-item__price text-end"
                                            style="min-width: 135px">

                                            <div class="fs-5">
                                                @{{ $formatPrice(item.priceSet.final_total.price) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Addresses --}}
                        <div class="">
                            <address-form type="shipping" :user="user"></address-form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 l-cart-page__sidebar">
                    <div class="card">
                        <div class="card-body l-cart-coupons border-bottom">

                        </div>

                        <div v-if="loaded" class="card-body l-cart-totals text-end">
                            <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100">
                                <div class="l-cart-total__label">
                                    訂單小計
                                </div>

                                <div class="l-cart-total__value">
                                    @{{ $formatPrice(totals.total.price, true) }}
                                </div>
                            </div>

                            <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100"
                                v-for="total of filteredTotals">
                                <div class="l-cart-total__label">
                                    @{{ total.label }}
                                </div>

                                <div class="l-cart-total__value">
                                    @{{ $formatPrice(total.price, true) }}
                                </div>
                            </div>

                            <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100 fs-5 fw-bold">
                                <div class="l-cart-total__label">
                                    訂單總計
                                </div>

                                <div class="l-cart-total__value">
                                    @{{ $formatPrice(totals.grand_total.price, true) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </cart-app>
    </div>

    <x-address-form></x-address-form>
    <x-vue.cascade-select></x-vue.cascade-select>
@stop
