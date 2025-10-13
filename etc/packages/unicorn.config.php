<?php

declare(strict_types=1);

namespace App\Config;

use Unicorn\Aws\S3Service;
use Unicorn\Listener\DumpOrphansSubscriber;
use Unicorn\Listener\UnicornAssetSubscriber;
use Unicorn\UnicornPackage;
use Unicorn\Upload\FileUploadOptions;
use Unicorn\Upload\ResizeConfig;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule(name: 'unicorn', enabled: true, priority: 100, belongsTo: UnicornPackage::class)]
static fn() => [
    'listeners' => [
        AssetService::class => [
            UnicornAssetSubscriber::class,
        ],
        AppContext::class => [
            DumpOrphansSubscriber::class,
        ],
    ],

    'providers' => [
        UnicornPackage::class,
    ],

    'modules' => [
        'next' => true
    ],

    'file_upload' => [
        'default' => 'default',

        /**
         * File upload settings.
         * --------------------------------------------------
         *
         * Options:
         * - storage: The Storage name
         * - accept: Accepted mime type or extensions, use (,) to separate multiple.
         * - dir: The default upload dir, use {year}, {month}, {day}, {hour}, {minute}, {second}, {ext}
         *          variables to replace file path.
         * - force_redraw: (bool) Force redraw image to wipe malware, must be TRUE if we allow front end uploads.
         * - resize:
         *      - enabled: (bool) Enable resize or not.
         *      - driver: `gd` or `imagick`.
         *      - strip_exif: (bool) Strip EXIF to reduce image size, only works for `imagick` driver.
         *      - width: (int) Image crop width or max width (px).
         *      - height: (int) Image crop height or max height (px).
         *      - crop: (bool) Crop image or not.
         *      - quality: (int) JPEG quality. (default: 85)
         *      - output_format: The output image format, if not provided, will auto detect.
         *      - orientate: (bool) Auto rotate images based on orientate info.
         * - raw_gif: (bool) If is gif file, use raw file, ignore redraw or resize.
         * - options: The extra options for different storages.
         *
         * --------------------------------------------------
         */
        'profiles' => [
            'default' => fn () => new FileUploadOptions(
                storage: env('UPLOAD_STORAGE_DEFAULT') ?: 'local',
                dir: 'files/{year}/{month}/{day}',
                accept: null,
                resize: new ResizeConfig(
                    enabled: false,
                    driver: env('IMAGE_RESIZE_DRIVER', 'gd'),
                ),
            ),
            'image' => fn () => new FileUploadOptions(
                storage: env('UPLOAD_STORAGE_DEFAULT') ?: 'local',
                dir: 'images/{year}/{month}/{day}',
                accept: 'image/*',
                rawGif: true,
                resize: new ResizeConfig(
                    enabled: true,
                    driver: env('IMAGE_RESIZE_DRIVER', 'gd'),
                    width: 1200,
                    height: 1200,
                    quality: 85,
                    crop: false,
                    outputFormat: null,
                ),
                storageOptions: [
                    'ACL' => S3Service::ACL_PUBLIC_READ,
                ],
            ),
            'frontend' => fn () => new FileUploadOptions(
                storage: env('UPLOAD_STORAGE_DEFAULT') ?: 'local',
                dir: 'files/{year}/{month}/{day}',
                accept: 'image/*',
                forceRedraw: true,
                resize: new ResizeConfig(
                    enabled: true,
                    driver: env('IMAGE_RESIZE_DRIVER', 'gd'),
                    width: 1200,
                    height: 1200,
                    quality: 85,
                    crop: false,
                    outputFormat: null,
                ),
                storageOptions: [
                    'ACL' => S3Service::ACL_PUBLIC_READ,
                ],
            ),
        ],
    ],
];
