<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Feedback\FeedbackPackage;

return [
    'feedback' => [
        'rating' => [
            'ajax_type_protect' => true,
            'ajax_allow_types' => [
                'comment',
            ],
        ],

        'providers' => [
            FeedbackPackage::class
        ]
    ]
];
