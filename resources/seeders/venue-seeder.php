<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\EventBooking\Entity\Venue;
use Lyrasoft\EventBooking\EventBookingPackage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Venue Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        EventBookingPackage $eventBooking
    ) use (
        $seeder,
        $orm,
        $db
    ) {
        $faker = $seeder->faker($eventBooking->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Venue> $mapper */
        $mapper = $orm->mapper(Venue::class);

        foreach (range(1, 7) as $i) {
            $item = $mapper->createEntity();

            $item->setTitle($faker->sentence(1));
            $item->setUrl($faker->url());
            $item->setMapUrl('https://www.google.com/maps/search/夏木樂/');
            $item->setAddress($faker->address());
            $item->setImage($faker->unsplashImage(800, 600));
            $item->setDescription($faker->paragraph());
            $item->setLinks(
                [
                    [
                        'url' => $faker->url(),
                        'title' => $faker->sentence(1),
                    ],
                    [
                        'url' => $faker->url(),
                        'title' => $faker->sentence(1),
                    ],
                ]
            );
            $item->setState(1);

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Venue::class);
    }
);
