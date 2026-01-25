<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Feedback\FeedbackPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('feedback', enabled: true, priority: 100, belongsTo: FeedbackPackage::class)]
static fn() => [
    'rating' => [
        'ajax_type_protect' => true,
        'ajax_allow_types' => [
            'comment',
        ],
    ],

    'providers' => [
        FeedbackPackage::class,
    ],
];
