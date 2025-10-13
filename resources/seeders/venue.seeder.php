<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\EventBooking\Entity\Venue;
use Lyrasoft\EventBooking\EventBookingPackage;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Venue Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(EventBookingPackage $eventBooking): void
    {
        $faker = $this->faker($eventBooking->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Venue> $mapper */
        $mapper = $this->orm->mapper(Venue::class);

        foreach (range(1, 7) as $i) {
            $item = $mapper->createEntity();

            $item->title = $faker->sentence(1);
            $item->url = $faker->url();
            $item->mapUrl = 'https://www.google.com/maps/search/夏木樂/';
            $item->address = $faker->address();
            $item->image = $faker->unsplashImage(800, 600);
            $item->description = $faker->paragraph();
            $item->links = [
                [
                    'url' => $faker->url(),
                    'title' => $faker->sentence(1),
                ],
                [
                    'url' => $faker->url(),
                    'title' => $faker->sentence(1),
                ],
            ];
            $item->state = 1;

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Venue::class);
    }
};
