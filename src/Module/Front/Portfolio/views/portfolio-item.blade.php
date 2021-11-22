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

/**
 * @var \App\Entity\Portfolio $item
 */
?>

@extends('global.body')

@section('content')
    <div class="container l-portfolio-item my-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <article>
                    <div class="l-portfolio-item__cover mb-4">
                        <img class="img-fluid" src="{{ $item->getCover() }}" alt="cover">
                    </div>

                    <header class="l-portfolio-item__header">
                        <h2>{{ $item->getTitle() }}</h2>
                    </header>

                    <div class="article-content l-portfolio-item__content">
                        {!! $item->getDescription() !!}
                    </div>

                    <div class="row gx-2 mt-4 l-portfolio-item__images">
                        @foreach ($item->getImages() as $image)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                <img class="img-fluid" src="{{ $image['url'] }}" alt="image">
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </div>
    </div>
@stop
