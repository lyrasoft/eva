<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Windwalker\Core\Form\FormProvider;
use Windwalker\Core\Provider\RendererProvider;
use Windwalker\Core\Theme\BootstrapTheme;
use Windwalker\Core\Theme\ThemeInterface;
use Windwalker\Renderer\EdgeRenderer;
use Windwalker\Renderer\MustacheRenderer;
use Windwalker\Renderer\PlatesRenderer;
use Windwalker\Renderer\TwigRenderer;

return [
    'paths' => [
        __DIR__ . '/../../views'
    ],

    'renderers' => [
        'edge' => [
            EdgeRenderer::class,
            ['edge.php', 'blade.php']
        ],
        // We use edge to replace blade
        // 'blade' => [
        //     BladeRenderer::class,
        //     ['blade.php']
        // ],
        'plates' => [
            PlatesRenderer::class,
            ['php']
        ],
        'mustache' => [
            MustacheRenderer::class,
            ['mustache']
        ],
        'twig' => [
            TwigRenderer::class,
            ['twig']
        ],
    ],

    'options' => [
        'cache_path' => WINDWALKER_CACHE . '/renderer'
    ],

    'pagination' => [
        'template' => 'layout.pagination.basic-pagination',
        'neighbours' => 4
    ],

    'providers' => [
        RendererProvider::class,
        FormProvider::class
    ],

    'bindings' => [
        ThemeInterface::class => BootstrapTheme::class,
    ],

    'extends' => [
        //
    ]
];
