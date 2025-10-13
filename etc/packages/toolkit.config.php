<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Toolkit\ToolkitPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule(name: 'toolkit', enabled: true, priority: 100, belongsTo: ToolkitPackage::class)]
static fn() => [
    'providers' => [
        ToolkitPackage::class,
    ],
];
