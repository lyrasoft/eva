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

use App\Entity\Formkit;
use App\Formkit\FormkitService;
use App\Formkit\Type\AbstractFormType;
use Unicorn\Script\UnicornScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Data\Collection;
use Windwalker\Form\Form;

use function Windwalker\collect;
use function Windwalker\uid;

/**
 * @var $item           Formkit
 * @var $form           Form
 * @var $fields         Collection<AbstractFormType>
 * @var $options        array
 * @var $formkitService FormkitService
 */

$asset->js('js/formkit.js');

$uid = uid();
$formId = $options['id'] ?? 'formkit-' . $uid;

$uniScript = $app->retrieve(UnicornScript::class);

$options = collect($options);

$return = $options['return'];

$uniScript->addRoute(
    'formkit.action.' . $uid,
    $nav->to('formkit_submit')
        ->id($item->getId())
        ->var('ns', $form->getNamespace())
        ->withReturn(return: $return)
        ->full()
);

?>
<div id="{{ $formId }}-wrapper" class="l-formkit-wrapper mb-5 mt-5" data-role="formkit"
    uni-formkit="{{ $uid }}">
    <form id="{{ $formId }}" method="post" enctype="multipart/form-data"
        uni-form-validate='{"scroll": true}'>
        <div class="l-formkit-content mb-5">
            @if (trim($item->getDescription()))
                <div class="l-formkit-content__desc">
                    {!! $item->getDescription() !!}
                </div>
            @endif
        </div>

        @foreach ($fields as $field)
            @php($formField = $form[$field->getLabel()])
            @if (!$formField)
                @continue
            @endif
            <div class="c-formkit-field-wrapper mb-4" data-uid="{{ $formField->get('uid') }}">
                @include($formkitService->getFieldLayout($field))
            </div>
        @endforeach

        <div class="py-5 text-center">
            <button type="reset" class="btn btn-lg btn-outline-secondary"
                style="min-width: 150px">
                @lang('formkit.button.clear')
            </button>
            <button type="button" class="btn btn-lg btn-primary"
                data-task="submit"
                style="min-width: 250px"
                data-dos
            >
                @lang('formkit.button.submit')
            </button>
        </div>

        <div class="d-none">
            @formToken()
        </div>
    </form>
</div>
