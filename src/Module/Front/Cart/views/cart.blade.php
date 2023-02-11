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

$shopGoScript = $app->service(ShopGoScript::class);
$shopGoScript->vueUtilities();
$shopGoScript->productCart();

$vueScript = $app->service(VueScript::class);
$vueScript->vue();
$vueScript->animate();

$uniScript = $app->service(UnicornScript::class);
$uniScript->data('product.item.props', [

]);
$uniScript->data('image.default', $imagePlaceholder->placeholderSquare());

$uniScript->addRoute('@cart_ajax');
?>

@extends('global.body')

@section('content')
    <div class="l-cart-page container my-5">
        <cart-app>
            <div class="row">
                <div class="col-lg-9 l-cart-page__content">
                    <header class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="m-0">購物車</h3>

                        <div>
                            <a href="javascript://">
                                <i class="fa fa-times"></i>
                                移除所有商品
                            </a>
                        </div>
                    </header>

                    <div class="l-cart-items">

                    </div>
                </div>

                <div class="col-lg-3 l-cart-page__sidebar">

                </div>
            </div>
        </cart-app>
    </div>
@stop
