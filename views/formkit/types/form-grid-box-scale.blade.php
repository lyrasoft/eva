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
use App\Formkit\Type\FormGridBoxScale;
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
 * @var $field          FormGridBoxScale
 * @var $formField      AbstractField
 */

$data = $field->getData();
?>

<x-field :field="$formField">
    <x-slot name="defaultSlot">
        <div class="c-grid-scale c-grid-scale--box" @attr('required', $formField->isRequired())>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="40%"></th>
                    @foreach ($data->columns as $column)
                        <th>
                            {{ $column['text'] }}
                        </th>
                    @endforeach
                </tr>
                </thead>

                <tbody>
                @foreach ($data->rows as $i => $row)
                    <tr uni-field-validate='{"inputOptions": true}' data-field-input
                        @attr('required', $formField->isRequired())>
                        <td>
                            <div class="c-row-text">
                                {{ $row['text'] }}
                            </div>
                        </td>

                        @foreach ($data->columns as $column)
                            <td>
                                <input type="checkbox"
                                    class="form-check-input"
                                    name="{{ $formField->getInputName("[{$row['text']}][]") }}"
                                    value="{{ $column['text'] }}" />
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-slot>
</x-field>
