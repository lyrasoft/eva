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

?>

@extends('global.html')

@section('superbody')
<div class="page" uni-cloak>
    {{-- Sidebar --}}
    @section('sidebar')
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- LOGO --}}
                <div class="navbar-brand navbar-brand-autodark">
                    <a href="{{ $nav->to('home') }}">
                        <img  src="{{ $asset->path('images/logo-cw-h.svg') }}"
                            alt="LOGO" style="width: auto; height: 35px;">
                    </a>
                </div>

                {{-- Sidemenu --}}
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        @include('global.layout.sidemenu')
                    </ul>
                </div>
            </div>
        </aside>

        {{--<div class="vertical-menu">--}}
        {{--    <div data-simplebar class="h-100">--}}
        {{--        <!--- Sidemenu -->--}}
        {{--        <div id="sidebar-menu">--}}
        {{--            <!-- Left Menu Start -->--}}
        {{--            <ul class="metismenu list-unstyled" id="side-menu">--}}
        {{--                @include('global.layout.sidemenu')--}}
        {{--            </ul>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--</div>--}}
    @show

    <div class="page-wrapper">
    {{-- Header --}}
    @section('header')
        @include('admin.global.layout.header')
    @show

    {{-- Main Container --}}
    @section('container')
    <div class="main-content" style="overflow: visible">

        <div class="page-content">
            <div class="container-fluid pt-3">
                @yield('body', 'Body Section')
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        @section('copyright')
            @include('admin.global.layout.footer')
        @show
    @show
    </div>
</div>
@stop
