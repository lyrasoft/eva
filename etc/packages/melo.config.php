<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Melo\Enum\OrderNoMode;
use Lyrasoft\Melo\Features\Question\Boolean\BooleanQuestion;
use Lyrasoft\Melo\Features\Question\MultiSelect\MultiSelectQuestion;
use Lyrasoft\Melo\Features\Question\Select\SelectQuestion;
use Lyrasoft\Melo\Features\Section\Homework\HomeworkSection;
use Lyrasoft\Melo\Features\Section\Quiz\QuizSection;
use Lyrasoft\Melo\Features\Section\Video\VideoSection;
use Lyrasoft\Melo\MeloPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('melo', enabled: true, priority: 100, belongsTo: MeloPackage::class)]
static fn() => [
    'providers' => [
        MeloPackage::class,
    ],

    'checkout' => [
        'allow_anonymous' => false,
        'default_expiry' => '+7days',
        'receiver_roles' => ['superuser', 'manager', 'admin'],
    ],

    'order_no' => [
        'mode' => OrderNoMode::INCREMENT_ID,
        'prefix' => 'S',
        'hash_offsets' => 100000,
        'sequence_day_format' => 'Ymd',
        'maxlength' => 20,
    ],

    'shop' => [
        'sitename' => 'Melo',
        'logo' => 'vendor/lyrasoft/melo/images/simular-logo.png',
    ],

    'upload_profiles' => [
        's3_multipart_storage' => 's3',
        'file' => 'default',
        'image' => 'image',
    ],

    'teachers' => [
        'roles' => ['teacher'],
    ],

    'sections' => [
        'defines' => [
            VideoSection::class,
            HomeworkSection::class,
            QuizSection::class,
        ]
    ],

    'questions' => [
        'defines' => [
            BooleanQuestion::class,
            SelectQuestion::class,
            MultiSelectQuestion::class,
        ]
    ]
];
