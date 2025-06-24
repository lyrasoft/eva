<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\EventBooking\Entity\Event;
use Lyrasoft\EventBooking\Entity\EventPlan;
use Lyrasoft\EventBooking\Entity\EventStage;
use Lyrasoft\EventBooking\Entity\Venue;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\Luna\Entity\Category;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Event Seeder
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

        /** @var EntityMapper<Event> $mapper */
        $mapper = $orm->mapper(Event::class);

        /** @var EntityMapper<EventStage> $stageMapper */
        $stageMapper = $orm->mapper(EventStage::class);

        /** @var EntityMapper<EventPlan> $planMapper */
        $planMapper = $orm->mapper(EventPlan::class);

        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'event'])->dump();
        $venueIds = $orm->findColumn(Venue::class, 'id')->dump();

        $createImage = function (int $w = 1200, int $h = 800) use ($faker) {
            return ['url' => $faker->unsplashImage($w, $h)];
        };

        foreach (range(1, 30) as $i) {
            $currentDate = $faker->dateTimeThisYear();

            $item = $mapper->createEntity();

            $item->setCategoryId((int) $faker->randomElement($categoryIds));
            $item->setTitle($faker->sentence(2));
            $item->setAlias(
                SlugHelper::safe($item->getTitle())
            );
            $item->setSubtitle($faker->sentence(3));
            $item->setCover($faker->unsplashImage(1200, 800));
            $item->setImages(
                [
                    $createImage(),
                    $createImage(),
                    $createImage(),
                ]
            );
            $item->setIntro($faker->sentence(5));
            $item->setDescription($faker->paragraph(3));
            $item->setState(1);

            $event = $mapper->createOne($item);

            foreach (range(1, 4) as $s) {
                $stage = $stageMapper->createEntity();

                $stage->setEventId($event->getId());
                $stage->setVenueId((int) $faker->randomElement($venueIds));
                $stage->setTitle('Stage ' . $s);
                $stage->setCover($faker->unsplashImage(1200, 800));
                $stage->setImages(
                    [
                        $createImage(),
                        $createImage(),
                        $createImage(),
                    ]
                );
                $stage->setDescription($faker->paragraph(3));
                $stage->setQuota(20);
                $stage->setAlternate(5);
                $stage->setLess(5);
                $stage->setState(1);
                $stage->setOrdering($s);
                $stage->setStartDate($currentDate = $currentDate->modify('+2months'));
                $stage->setEndDate($currentDate->modify('+14days'));

                $stage = $stageMapper->createOne($stage);

                // Plans
                $plan = new EventPlan();
                $plan->setEventId($event->getId());
                $plan->setStageId($stage->getId());
                $plan->setTitle('Early Access');
                $plan->setEndDate($stage->getStartDate()->modify('-30days'));
                $plan->setState(1);
                $plan->setQuota(5);
                $plan->setOnceMax(1);
                $plan->setOriginPrice(800);
                $plan->setPrice(500);

                $planMapper->createOne($plan);

                $plan = new EventPlan();
                $plan->setEventId($event->getId());
                $plan->setStageId($stage->getId());
                $plan->setTitle('Basic Ticket');
                $plan->setStartDate($stage->getStartDate()->modify('-30days'));
                $plan->setState(1);
                $plan->setQuota($stage->getQuota());
                $plan->setOnceMax(1);
                $plan->setPrice(800);

                $planMapper->createOne($plan);

                $seeder->outCounting();
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Event::class, EventStage::class, EventPlan::class);
    }
);
