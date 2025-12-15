<?php

declare(strict_types=1);

namespace App\Config;

use Lyrasoft\Throttle\Enum\RateLimitPolicy;
use Lyrasoft\Throttle\Factory\LockServiceFactory;
use Lyrasoft\Throttle\Factory\RateLimiterServiceFactory;
use Lyrasoft\Throttle\Lock\LockDbStore;
use Lyrasoft\Throttle\RateLimiter\RateLimitDbStorage;
use Lyrasoft\Throttle\ThrottlePackage;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule(name: 'throttle', enabled: true, priority: 100, belongsTo: ThrottlePackage::class)]
static fn() => [
    'providers' => [
        ThrottlePackage::class,
    ],

    'default_lock' => 'default',

    'default_rate_limiter' => 'default',

    'factories' => [
        LockFactory::class => [
            'default' => fn () => LockServiceFactory::factory(
                storage: 'default',
            ),
        ],
        RateLimiterFactoryInterface::class => [
            'default' => fn () => RateLimiterServiceFactory::factory(
                id: 'default',
                policy: RateLimitPolicy::FIXED_WINDOW,
                limit: 10,
                interval: '1 minute',
                locker: true,
                storage: 'default',
            ),
        ],

        // Lock Stores
        PersistingStoreInterface::class => [
            'default' => LockDbStore::class,
            'cache' => fn () => LockServiceFactory::cacheStorage(),
        ],

        // RateLimiter Storages
        StorageInterface::class => [
            'default' => RateLimitDbStorage::class,
            'cache' => fn () => RateLimiterServiceFactory::cacheStorage()
        ],
    ]
];
