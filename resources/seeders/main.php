<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 LYRASOFT.
 * @license    __LICENSE__
 */

declare(strict_types=1);

\Lyrasoft\Luna\Faker\FakerHelper::registerMoreLoremClasses();

return [
    __DIR__ . '/base/user-seeder.php',
    __DIR__ . '/base/config-seeder.php',
    __DIR__ . '/base/language-seeder.php',
    __DIR__ . '/base/category-seeder.php',
    __DIR__ . '/base/tag-seeder.php',
    __DIR__ . '/base/article-seeder.php',
    __DIR__ . '/base/page-seeder.php',
    // __DIR__ . '/menu-seeder.php',
    __DIR__ . '/base/widget-seeder.php',
    __DIR__ . '/payment-seeder.php',
    __DIR__ . '/shipping-seeder.php',
    __DIR__ . '/manufacturer-seeder.php',
    __DIR__ . '/product-feature-seeder.php',
    __DIR__ . '/product-attribute-seeder.php',
    __DIR__ . '/product-tab-seeder.php',
    __DIR__ . '/product-seeder.php',
    __DIR__ . '/discount-seeder.php',
    __DIR__ . '/address-seeder.php',
    __DIR__ . '/additional-purchase-seeder.php',
    __DIR__ . '/order-seeder.php',
];
