<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var  $vm        WishlistListView  The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use App\Module\Front\Wishlist\WishlistListView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>

@extends('global.body')

@section('content')
    <div class="container l-wishlist my-5">
        <h2>待買清單</h2>

        <div class="l-wishlist__items row">
            @foreach ($items as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <x-product-card
                        :item="$item"
                        :favorited="$item->favorited"
                    >
                    </x-product-card>
                </div>
            @endforeach
        </div>
    </div>
@stop
