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

// $body = $app->retrieve(\Windwalker\Core\Html\HtmlFrame::class)
//     ->getBodyElement();
//
// $body->setAttribute('data-bs-theme', null);
?>

@extends('global.html')

@section('superbody')
<div class="page" uni-cloak>
    {{-- Sidebar --}}
    @section('sidebar')
        @include('admin.global.layout.sidebar')
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
