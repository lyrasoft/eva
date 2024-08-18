<?php

declare(strict_types=1);

namespace App\Config;

use App\FormkitPackage;
use App\Formkit\Type\FormDate;
use App\Formkit\Type\FormFile;
use App\Formkit\Type\FormGridRadioScale;
use App\Formkit\Type\FormPointScale;
use App\Formkit\Type\FormSelect;
use App\Formkit\Type\FormCheckboxes;
use App\Formkit\Type\FormGridBoxScale;
use App\Formkit\Type\FormRadio;
use App\Formkit\Type\FormText;
use App\Formkit\Type\FormTextarea;
use App\Formkit\Type\FormTime;

return [
    'formkit' => [
        'enabled' => true,

        'providers' => [
            FormkitPackage::class,
        ],

        'view' => [
            'default_extends' => 'global.body',
            'extends' => [
                'global.body' => 'Default Layout',
            ]
        ],

        'types' => [
            FormText::getId() => FormText::class,
            FormTextarea::getId() => FormTextarea::class,
            FormRadio::getId() => FormRadio::class,
            FormCheckboxes::getId() => FormCheckboxes::class,
            FormSelect::getId() => FormSelect::class,
            FormPointScale::getId() => FormPointScale::class,
            FormGridRadioScale::getId() => FormGridRadioScale::class,
            FormGridBoxScale::getId() => FormGridBoxScale::class,
            FormDate::getId() => FormDate::class,
            FormTime::getId() => FormTime::class,
            FormFile::getId() => FormFile::class,
        ]
    ]
];
