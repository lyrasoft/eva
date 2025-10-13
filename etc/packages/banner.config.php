<?php

use App\Enum\BannerType;
use Lyrasoft\Banner\BannerPackage;
use Windwalker\Core\Attributes\ConfigModule;

return #[ConfigModule('banner', enabled: true, priority: 100, belongsTo: BannerPackage::class)]
static fn() => [
    'providers' => [
        BannerPackage::class,
    ],
    'widget' => [
        'upload_profile' => 'image',
    ],
    'type_enum' => BannerType::class,
    'video_enabled' => true,
    'types' => [
        '_default' => [
            'desktop' => [
                'width' => 1920,
                'height' => 800,
                'crop' => true,
                'ajax' => false,
                'image_ext' => 'jpg',
                'profile' => 'image',
            ],
            'mobile' => [
                'width' => 720,
                'height' => 720,
                'crop' => true,
                'ajax' => false,
                'image_ext' => 'jpg',
                'profile' => 'image',
            ],
        ],
    ],
];
