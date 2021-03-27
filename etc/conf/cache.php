<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Windwalker\Cache\CachePool;
use Windwalker\Cache\Serializer\PhpSerializer;
use Windwalker\Cache\Serializer\RawSerializer;
use Windwalker\Cache\Storage\FileStorage;
use Windwalker\Cache\Storage\NullStorage;
use Windwalker\Core\Manager\CacheManager;
use Windwalker\Core\Manager\LoggerManager;
use Windwalker\DI\Container;

return [
    'no_cache' => env('CACHE_DISABLED'),

    // The default cache profile
    'default' => 'global',

    'providers' => [

    ],

    'bindings' => [
        CacheManager::class,
    ],

    'factories' => [
        'instances' => [
            'none' => static fn(): CachePool => new CachePool(new NullStorage()),
            'global' =>
            static function (Container $container, string $instanceName): CachePool {
                return new CachePool(
                    $container->resolve('cache.factories.storages.file', compact('instanceName')),
                    new PhpSerializer(),
                    $container->get(LoggerManager::class)->get('error')
                );
            },
            'html' => static fn(Container $container, string $instanceName): CachePool => new CachePool(
                $container->resolve('cache.factories.storages.file', compact('instanceName')),
                new RawSerializer(),
                $container->get(LoggerManager::class)->get('error')
            ),
        ],
        'storages' => [
            'file' => static fn(Container $container, string $instanceName): FileStorage => new FileStorage(
                $container->getParam('@cache') . '/' . $instanceName,
                []
            ),
        ],
    ],
];
