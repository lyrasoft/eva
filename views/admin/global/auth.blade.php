<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $view      ViewModel       The view modal object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>

@extends('global.html')

@section('superbody')
    <div class="page">
        <div class="container container-tight py-5 mt-5">
            @yield('header')

            <div class="card card-md">
                @yield('card-start')

                <div class="card-body">
                    @section('logo')
                        <div class="text-center mb-4">
                            <a href="{{ $uri->root() }}" target="_blank">
                                <img style="height: 45px" src="{{ $asset->path('images/logo-cb-h.svg') }}" alt="LOGO">
                            </a>
                        </div>
                    @show

                    @section('card-title')
                        <div class="text-center mb-4">
                            <h2 class="h2">
                                後台管理
                            </h2>
                        </div>
                    @show

                    @section('message')
                        @include('@messages')
                    @show

                    @yield('container', 'Container')
                </div>

                @yield('card-end')
            </div>

            @yield('footer')
        </div>
    </div>
@stop
