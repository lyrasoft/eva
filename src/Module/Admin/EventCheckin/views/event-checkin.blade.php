<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        EventCheckinView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\EventAttend;
use App\Module\Admin\EventCheckin\EventCheckinView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $attend EventAttend
 */
?>

@extends('admin.global.body')

@section('superbody')
    <div class="my-5">
        <div class="text-center">
            @if ($status ?? '' === 'fail')
                <h2>報到失敗</h2>

                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @else
                <h2>報到成功</h2>

                <div class="mb-3">
                    編號： #{{ $attend->getNo() }}
                </div>

                <div>
                    姓名： {{ $attend->getName() }}
                    @if ($attend->getNick())
                        ({{ $attend->getNick() }})
                    @endif
                </div>
            @endif
        </div>
    </div>
@stop
