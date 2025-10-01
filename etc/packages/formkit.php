<?php

declare(strict_types=1);

namespace Lyrasoft\Formkit\Config;

use Lyrasoft\Formkit\Formkit\Type\FormCheckboxes;
use Lyrasoft\Formkit\Formkit\Type\FormDate;
use Lyrasoft\Formkit\Formkit\Type\FormFile;
use Lyrasoft\Formkit\Formkit\Type\FormGridBoxScale;
use Lyrasoft\Formkit\Formkit\Type\FormGridRadioScale;
use Lyrasoft\Formkit\Formkit\Type\FormPointScale;
use Lyrasoft\Formkit\Formkit\Type\FormRadio;
use Lyrasoft\Formkit\Formkit\Type\FormSelect;
use Lyrasoft\Formkit\Formkit\Type\FormText;
use Lyrasoft\Formkit\Formkit\Type\FormTextarea;
use Lyrasoft\Formkit\Formkit\Type\FormTime;
use Lyrasoft\Formkit\FormkitPackage;

return [
    'formkit' => [
        'enabled' => true,

        'providers' => [
            FormkitPackage::class,
        ],

        'receivers' => [
            'roles' => [
                'superuser',
                'admin'
            ],
            'cc' => [
                //
            ],
            'bcc' => [
                //
            ],
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
