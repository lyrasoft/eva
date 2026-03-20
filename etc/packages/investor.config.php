<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Investor\InvestorPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('investor', enabled: true, priority: 100, belongsTo: InvestorPackage::class)]
static fn() => [
    'providers' => [
        InvestorPackage::class,
    ],

    'bindings' => [
        //
    ],
];
