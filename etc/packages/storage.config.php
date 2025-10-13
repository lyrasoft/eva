<?php

declare(strict_types=1);

namespace App\Config;

use Aws\S3\S3Client;
use Unicorn\Aws\S3Service;
use Unicorn\Flysystem\FlysystemFactory;
use Unicorn\Provider\StorageProvider;
use Unicorn\Storage\Adapter\S3Storage;
use Unicorn\Storage\StorageFactory;
use Unicorn\Storage\StorageInterface;
use Unicorn\UnicornPackage;
use Unicorn\Upload\FileUploadService;
use Unicorn\Upload\FileUploadSubscriber;
use Windwalker\Core\Attributes\ConfigModule;
use Windwalker\DI\Container;

return #[ConfigModule(name: 'storage', enabled: true, priority: 100, belongsTo: UnicornPackage::class)]
static fn() => [
    'default' => env('UPLOAD_STORAGE_DEFAULT') ?: 'local',

    's3' => [
        'default_region' => 'ap-northeast-1',
    ],

    'providers' => [
        StorageProvider::class,
    ],

    'bindings' => [
        S3Client::class => static function (Container $container) {
            /** @var S3Storage $storage */
            $storage = $container->get(StorageInterface::class, tag: 's3');

            return $storage->getS3Service()->getClient();
        },
        S3Service::class => static function (Container $container) {
            /** @var S3Storage $storage */
            $storage = $container->get(StorageInterface::class, tag: 's3');

            return $storage->getS3Service();
        },
    ],

    'listeners' => [
        FileUploadService::class => [
            FileUploadSubscriber::class,
        ],
    ],

    'factories' => [
        'instances' => [
            'local' => static fn(StorageFactory $factory) => $factory->localStorage(
                [
                    'path' => env('STORAGE_LOCAL_PATH') ?? 'www/assets/upload',
                    'uri_base' => env('STORAGE_LOCAL_URI_BASE') ?? 'assets/upload',
                ]
            ),
            's3' => static fn(StorageFactory $factory) => $factory->s3Storage(
                [
                    'access_key' => env('AWS_ACCESS_KEY'),
                    'secret' => env('AWS_SECRET'),
                    'bucket' => env('AWS_S3_BUCKET'),
                    'subfolder' => env('AWS_S3_SUBFOLDER'),
                    'endpoint' => env('AWS_S3_ENDPOINT'),
                    'region' => env('AWS_S3_REGION'),
                ]
            ),
            'flys3' => static fn(StorageFactory $factory) => $factory->flysystem(
                fn(FlysystemFactory $factory) => $factory->s3v3Adapter(
                    [
                        'access_key' => env('AWS_ACCESS_KEY'),
                        'secret' => env('AWS_SECRET'),
                        'bucket' => env('AWS_S3_BUCKET'),
                        'subfolder' => env('AWS_S3_SUBFOLDER'),
                        'endpoint' => env('AWS_S3_ENDPOINT'),
                        'region' => env('AWS_S3_REGION'),
                    ]
                )
            ),
        ],
    ],
];
