<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 LYRASOFT.
 * @license    __LICENSE__
 */

declare(strict_types=1);

\Lyrasoft\Luna\Faker\FakerHelper::registerChineseLorem();

return [
    __DIR__ . '/user-seeder.php',
    __DIR__ . '/config-seeder.php',
    __DIR__ . '/category-seeder.php',
    __DIR__ . '/tag-seeder.php',
    __DIR__ . '/article-seeder.php',
    __DIR__ . '/page-seeder.php',
    __DIR__ . '/menu-seeder.php',
    __DIR__ . '/member-seeder.php',
    __DIR__ . '/portfolio-seeder.php',
    __DIR__ . '/contact-seeder.php',
];
