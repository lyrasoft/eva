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
    <div class="container l-member-list my-5">
        <div class="row">
            @foreach ($items as $item)
                <div class="col-lg-3 col-md-4 col-sm-2">
                    <div class="card mb-3">
                        <img class="card-img-top" src="{{ $item->getImage() }}" alt="Image">

                        <div class="card-body">
                            <div>
                                <h4 class="card-title">
                                    {{ $item->getName() }}
                                </h4>
                                <div>
                                    {!! $item->getIntro() !!}
                                </div>
                                <div class="mt-3">
                                    <a class="btn btn-primary"
                                        href="{{ $nav->to('member_item')->id($item->getId())->alias($item->getAlias()) }}">
                                        觀看更多
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
