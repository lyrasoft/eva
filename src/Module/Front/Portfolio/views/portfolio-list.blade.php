<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        PortfolioListView  The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use App\Module\Front\Portfolio\PortfolioListView;
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
    <div class="container l-portfolio-list my-5">
        @foreach ($items as $item)
            <div class="card mb-3">
                <div class="card-body d-flex">
                    <div class="me-3" style="min-width: 300px; width: 300px">
                        <img class="img-fluid" src="{{ $item->getCover() }}" alt="Cover">
                    </div>
                    <div>
                        <h4 class="card-title">
                            {{ $item->getTitle() }}
                        </h4>
                        <div>
                            {!! $item->getDescription() !!}
                        </div>
                        <div class="mt-3">
                            <a class="btn btn-primary"
                                href="{{ $nav->to('portfolio_item')->id($item->getId())->alias($item->getAlias()) }}">
                                觀看更多
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
