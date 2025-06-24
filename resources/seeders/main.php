<?php

declare(strict_types=1);

\Lyrasoft\Luna\Faker\FakerHelper::registerMoreLoremClasses();

return [
    __DIR__ . '/user-seeder.php',
    __DIR__ . '/config-seeder.php',
    __DIR__ . '/language-seeder.php',
    __DIR__ . '/category-seeder.php',
    __DIR__ . '/tag-seeder.php',
    __DIR__ . '/article-seeder.php',
    // Events
    __DIR__ . '/venue-seeder.php',
    __DIR__ . '/event-seeder.php',
    __DIR__ . '/event-order-seeder.php',

    // Feedback
    __DIR__ . '/comment-seeder.php',
    __DIR__ . '/rating-seeder.php',

    // Formkit
    __DIR__ . '/formkit-seeder.php',

    // Contact
    __DIR__ . '/contact-seeder.php',

    // Banner
    __DIR__ . '/banner-seeder.php',

    __DIR__ . '/page-seeder.php',
    __DIR__ . '/menu-seeder.php',
    __DIR__ . '/widget-seeder.php',
];
