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
 * @var \App\Entity\Member $item
 */
?>

@extends('global.body')

@section('content')
    <div class="container l-member-item my-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <article class="row">
                    <div class="l-member-item__cover col-md-4 mb-4">
                        <img class="img-fluid" src="{{ $item->getImage() }}" alt="cover">
                    </div>

                    <div class="col">
                        <header class="l-member-item__name">
                            <h2>{{ $item->getName() }}</h2>
                        </header>

                        <div class="article-content l-member-item__content">
                            {!! html_escape($item->getIntro(), true) !!}
                            {!! $item->getDescription() !!}
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
@stop
