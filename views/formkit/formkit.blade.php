<?php

declare(strict_types=1);

namespace App\view;

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

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>
<div id="{{ $formId }}-wrapper" class="l-formset-wrapper mb-5 mt-5" data-role="formset">
    <form id="{{ $formId }}" action="{{ $action }}" method="post" enctype="multipart/form-data">
        <div class="l-formset-content mb-5">
            @if (trim($formset->description))
                <div class="l-formset-content__desc">
                    {!! $formset->description !!}
                </div>
            @endif
        </div>

        @foreach ($form->getFields() as $field)
            <div class="c-formset-field" data-uid="{{ $field->get('uid') }}">
                {!! $field->render(['vertical' => true]) !!}
            </div>
        @endforeach

        <div class="py-5 text-center">
            <button type="reset" class="btn btn-lg btn-outline-secondary"
                style="min-width: 150px">
                @lang('tigcr.formset.button.clear')
            </button>
            <button type="submit" class="btn btn-lg btn-secondary disable-on-submit"
                style="min-width: 250px">
                @lang('tigcr.formset.button.submit')
            </button>
        </div>

        <div class="d-none">
            @formToken()
        </div>
    </form>
</div>
