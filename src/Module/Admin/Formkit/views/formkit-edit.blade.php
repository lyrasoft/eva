<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        FormkitEditView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use App\Entity\Formkit;
use App\Formkit\FormkitService;
use App\Formkit\Type\AbstractFormType;
use App\Module\Admin\Formkit\FormkitEditView;
use Unicorn\Script\UnicornScript;
use Unicorn\Script\VueScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Form\Form;

/**
 * @var $form Form
 * @var $item Formkit
 */

$uniScript = $app->retrieve(UnicornScript::class);
$vueScript = $app->retrieve(VueScript::class);
$formkitService = $app->retrieve(FormkitService::class);

$vueScript->vue();
$vueScript->animate();
$vueScript->draggable();

$asset->js('js/formkit-edit/index.js');

$types = $formkitService->getFormTypes()
    ->map(
        function ($formType) {
            /** @var class-string<AbstractFormType> $formType */
            return [
                'id' => $formType::getId(),
                'name' => $formType::getName(),
                'icon' => $formType::getIcon(),
                'params' => $formType::getDefaultParams(),
                'description' => $formType::getDescription(),
            ];
        }
    );

$uniScript->data(
    'formkit.props',
    [
        'fields' => $item->getContent(),
        'types' => $types,
        'item' => $item,
        'name' => 'item[content]'
    ]
);

?>

@extends('admin.global.body-edit')

@section('toolbar-buttons')
    @include('edit-toolbar')
@stop

@section('content')
    <form name="admin-form" id="admin-form"
        uni-form-validate='{"scroll": true}'
        action="{{ $nav->to('formkit_edit') }}"
        method="POST" enctype="multipart/form-data">

        <div class="row">
            <div class="col-lg-8">
                <formkit-edit-app id="formkit-edit-app" />
            </div>
            <div class="col-lg-4">
                <x-fieldset name="basic" :title="$lang('unicorn.fieldset.basic')"
                    :form="$form"
                    class="mb-4"
                    is="card"
                >
                </x-fieldset>

                <x-fieldset name="meta" :title="$lang('unicorn.fieldset.meta')"
                    :form="$form"
                    class="mb-4"
                    is="card"
                >
                </x-fieldset>
            </div>
        </div>

        <div class="d-none">
            @if ($idField = $form?->getField('item/id') ?? $form?->getField('id'))
                <input name="{{ $idField->getInputName() }}" type="hidden" value="{{ $idField->getValue() }}" />
            @endif

            <x-csrf></x-csrf>
        </div>
    </form>
@stop
