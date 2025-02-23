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

$htmlFrame = $app->service(\Windwalker\Core\Html\HtmlFrame::class);

$body = $htmlFrame->getBodyElement();

$body->addClass('layout-fluid');
?>

@extends('admin.global.body-wrapper')

@section('body')
    @section('banner')
        @include('admin.global.layout.banner')
    @show

    @yield('page-start')

    <section id="content-container" class="page-body">
        @section('content-container')
            @include('@messages')

            @yield('content', 'Admin Content')
        @show
    </section>

    @yield('page-end')
@stop
