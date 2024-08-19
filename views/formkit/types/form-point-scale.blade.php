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
use App\Formkit\Type\FormPointScale;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Data\Collection;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Form;

/**
 * @var $item           Formkit
 * @var $form           Form
 * @var $fields         Collection<AbstractFormType>
 * @var $options        array
 * @var $formkitService FormkitService
 * @var $field          FormPointScale
 * @var $formField      AbstractField
 */

$data = $field->getData();

$input = $formField->getPreparedInput();
$input->addClass('c-point-scale d-flex align-items-center gap-3');
?>

<x-field :field="$formField">
    <x-slot name="defaultSlot">
        <div {!! $input::buildAttributes($input) !!}>
            @if ($data->start)
                <div class="c-point-scale__start">
                    {{ $data->start }}
                </div>
            @endif

            @foreach (range((int) $data->min, (int) $data->max) as $i)
                <div class="c-point-scale__option mx-3" data-radio-item-wrapper
                    data-input-option>
                    <label for="input-{{ $data->uid }}-option-{{ $i }}" class="pb-2 mb-0">
                        {{ $i }}
                    </label>
                    <div class="mb-2">
                        <input id="input-{{ $data->uid }}-option-{{ $i }}"
                            type="radio"
                            class="form-check-input"
                            data-radio-item-input
                            name="{{ $formField->getInputName() }}"
                            value="{{ $i }}"
                        />
                    </div>
                </div>
            @endforeach

            @if ($data->end)
                <div class="c-point-scale__end">
                    {{ $data->end }}
                </div>
            @endif

        </div>
    </x-slot>
</x-field>

