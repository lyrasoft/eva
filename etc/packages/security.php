<?php

declare(strict_types=1);

use Lyrasoft\Security\SecurityPackage;

return [
    'security' => [
        'enabled' => true,

        'providers' => [
            SecurityPackage::class
        ]
    ]
];
