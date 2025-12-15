<?php

declare(strict_types=1);

use Lyrasoft\Security\SecurityPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('security', enabled: true, priority: 100, belongsTo: SecurityPackage::class)]
static fn() => [
    'providers' => [
        SecurityPackage::class,
    ],
];
