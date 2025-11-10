<?php

declare(strict_types=1);

\Lyrasoft\Luna\Faker\FakerHelper::registerMoreLoremClasses();

return [
    __DIR__ . '/user.seeder.php',
    __DIR__ . '/config.seeder.php',
    __DIR__ . '/language.seeder.php',
    __DIR__ . '/category.seeder.php',
    __DIR__ . '/tag.seeder.php',
    __DIR__ . '/article.seeder.php',

    // Banner
    __DIR__ . '/banner.seeder.php',

    // Member
    __DIR__ . '/member.seeder.php',

    // Portfolio
    __DIR__ . '/portfolio.seeder.php',

    // Events
    __DIR__ . '/venue.seeder.php',
    __DIR__ . '/event.seeder.php',
    __DIR__ . '/event-order.seeder.php',

    // Feedback
    __DIR__ . '/comment-seeder.php',
    __DIR__ . '/rating-seeder.php',

    // Formkit
    __DIR__ . '/formkit-seeder.php',

    // Contact
    __DIR__ . '/contact.seeder.php',

    // ShopGo
    __DIR__ . '/payment.seeder.php',
    __DIR__ . '/shipping.seeder.php',
    __DIR__ . '/manufacturer.seeder.php',
    __DIR__ . '/product-feature.seeder.php',
    __DIR__ . '/product-attribute.seeder.php',
    __DIR__ . '/product-tab.seeder.php',
    __DIR__ . '/product.seeder.php',
    __DIR__ . '/discount.seeder.php',
    __DIR__ . '/address.seeder.php',
    __DIR__ . '/additional-purchase.seeder.php',
    __DIR__ . '/order.seeder.php',

    // Luna
    __DIR__ . '/page.seeder.php',
    __DIR__ . '/menu.seeder.php',
    __DIR__ . '/widget.seeder.php',
];
