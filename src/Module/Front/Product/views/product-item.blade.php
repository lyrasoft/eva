<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        ProductItemView The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use App\Module\Front\Product\ProductItemView;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\ShopGo\Entity\Discount;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ProductFeature;
use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Script\ShopGoScript;
use Lyrasoft\ShopGo\Service\ProductAttributeService;
use Unicorn\Script\UnicornScript;
use Unicorn\Script\VueScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $item           Product
 * @var $variant        ProductVariant
 * @var $category       Category
 * @var $discounts      Discount[]
 * @var $attrGroups     Category[]
 * @var $attribute      ProductAttribute
 * @var $tabs           ProductTab[]
 * @var $features       ProductFeature[]
 */

$attributeService = $app->service(ProductAttributeService::class);

$app->service(ShopGoScript::class)->vueUtilities();

$vueScript = $app->service(VueScript::class);
$vueScript->vue();
$vueScript->animate();

$uniScript = $app->service(UnicornScript::class);
$uniScript->data('product.item.props', [
    'product' => $item,
    'features' => $features,
    'mainVariant' => $variant,
]);

$uniScript->addRoute('@product_ajax');

?>

@extends('global.body')

@section('content')

    <div class="l-product-item container my-5">

        <div id="product-item-app" class="row">
            <div class="col-lg-6 l-product-item__images l-product-images">
                <div class="l-product-images__hero border ratio ratio-1x1">
                    <img src="{{ $variant->getCover() }}" alt="Cover">
                </div>

                <div class="l-product-images__nav">

                </div>
            </div>
            <div class="col-lg-6 l-product-item__info l-product-info">
                {{-- Header --}}
                <header class="l-product-info__header d-flex flex-column gap-2">
                    {{-- Todo: tags --}}

                    <div class="d-flex align-items-center">
                        {{-- Title --}}
                        <h1 class="l-product-info__title m-0">{{ $item->getTitle() }}</h1>

                        {{-- Wishlist --}}
                        <div class="ms-auto l-product-info__favorite">
                            <a href="javascript://" class="fs-4">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Category --}}
                    @if ($category)
                        <div class="l-product-info__category">
                            {{ $category->getTitle() }}
                        </div>
                    @endif

                    <div class="">
                        {{-- Model --}}
                        <div class="l-product-info__model c-info d-inline-block">
                            <span class="c-info__label fw-bold">
                                型號
                            </span>

                            <span class="c-info__value">
                                {{ $item->getModel() }}
                            </span>
                        </div>
                        /
                        {{-- SKU --}}
                        <div class="l-product-info__sku c-info d-inline-block">
                            <span class="c-info__label fw-bold">
                                SKU
                            </span>

                            <span class="c-info__value">
                                {{ $variant->getSku() }}
                            </span>
                        </div>
                    </div>
                </header>

                {{-- Price --}}
                <div class="l-product-info__pricing l-pricing mt-4">
                    <div class="l-pricing__final c-price fs-5">
                        <span class="c-price__label">
                            原價
                        </span>
                        <del class="c-price__value">
                            {{ $vm->formatPrice($variant->getPriceSet()['origin']) }}
                        </del>
                    </div>

                    <div class="l-pricing__final c-price fs-4 fw-bold">
                        <span class="c-price__label">
                            優惠價
                        </span>
                        <span class="c-price__value">
                            {{ $vm->formatPrice($variant->getPriceSet()['final']) }}
                        </span>
                    </div>
                </div>

                <hr />

                {{-- Intro --}}
                <div class="l-product-info__intro l-intro">
                    {!! html_escape($item->getIntro(), true) !!}
                </div>

                {{-- Attributes --}}
                <div class="l-product-info__basic-info l-basic-info mt-4">
                    @foreach ($attrGroups as $group)
                        @if (!in_array('intro', $group->getParams()['position'] ?? [], true))
                            @continue
                        @endif

                        @php($attributes = $group->getParams()['attributes'] ?? [])

                        @foreach ($attributes as $attribute)
                            <div class="c-info">
                                <span class="c-info__label fw-bold">
                                    {{ $attribute->getTitle() }}
                                </span>

                                <span class="c-info__value">
                                    {{ $attributeService->renderValue($attribute) }}
                                </span>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                {{-- Discounts --}}
                @if (count($discounts))
                    <div class="l-product-info__discounts l-discounts mt-4">
                        <h5>多件折扣</h5>

                        <div>
                            @foreach ($discounts as $discount)
                                <div>
                                    購買 {{ $discount->getMinProductQuantity() }} 件以上，每個只要
                                    {{ $vm->formatPrice($discount->getPrice()) }}
                                    元
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (count($features))
                    <div class="l-product-info__features l-features mt-4">
                        <div class="l-feature-item mb-3" v-for="feature of features">
                            <div class="l-feature-item__title mb-2">
                                <strong>@{{ feature.title }}</strong>
                            </div>
                            <div class="l-feature-item__options d-flex gap-1">
                                <button class="btn btn-outline-primary btn-sm"
                                    v-for="option of feature.options"
                                    @click="toggleOption(option, feature)"
                                    :class="{ active: isSelected(option, feature) }"
                                >
                                    @{{ option.text }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="l-product-info__actions l-actions mt-4">

                    {{-- Quantity --}}
                    <div class="l-actions__quantity">
                        <div class="input-group">
                            <button type="button" class="btn btn-secondary">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="text" class="form-control" value="1" />
                            <button type="button" class="btn btn-secondary">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="l-actions__buttons mt-3 d-grid d-lg-flex gap-2">
                        <button type="button" class="btn btn-primary btn-lg flex-fill">
                            <i class="fa fa-cart-shopping"></i>
                            立即購買
                        </button>

                        <button type="button" class="btn btn-outline-primary btn-lg flex-fill">
                            <i class="fa fa-cart-plus"></i>
                            加入購物車
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <x-tabs>

                <x-tab name="description" title="商品介紹">
                    {!! $item->getDescription() !!}
                </x-tab>

                <x-tab name="attributes" title="商品規格">
                    Attributes
                </x-tab>

                @foreach ($tabs as $tab)
                    <x-tab :name="'tab-' . $tab->getId()" :title="$tab->getTitle()">
                        @include('product-tab', compact('tab'))
                    </x-tab>
                @endforeach
            </x-tabs>
        </div>

    </div>

@stop
