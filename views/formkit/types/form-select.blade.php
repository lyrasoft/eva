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
use App\Formkit\Type\FormSelect;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Data\Collection;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Form;

/**
 * @var $item           Formkit
 * @var $form           Form
 * @var $fields         Collection<AbstractFormType>
 * @var $options        array
 * @var $formkitService FormkitService
 * @var $field          FormSelect
 * @var $formField      ListField
 */

if ($field->hasOther()) {
    $formField->option(
        '其他',
        '其他',
    );
}
?>

<div class="l-form-select-wrapper">
    <x-input :field="$formField" :options="[]"></x-input>

    @if ($field->hasOther())
        <div class="mt-2">
            {!! $field->getOtherInput($formField) !!}
        </div>
    @endif
</div>
