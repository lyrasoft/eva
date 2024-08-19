<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        FormkitItemView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Formkit;
use App\Formkit\FormkitService;
use App\Module\Front\Formkit\FormkitItemView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $item Formkit
 */

$formkitService = $app->retrieve(FormkitService::class);
?>

@extends('global.body')

@section('content')
    <div class="container my-4">
        <div class="mx-auto" style="max-width: 850px">
            <header class="text-center mb-5">
                <h2>{{ $item->getTitle() }}</h2>
            </header>

            {!! $formkitService->render($item) !!}
        </div>
    </div>
@stop
