<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\EventBooking\Entity\Event;
use Lyrasoft\EventBooking\Entity\EventPlan;
use Lyrasoft\EventBooking\Entity\EventStage;
use Lyrasoft\EventBooking\Entity\Venue;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\Luna\Entity\Category;
use Unicorn\Enum\BasicState;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Event Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(EventBookingPackage $eventBooking): void
    {
        $faker = $this->faker($eventBooking->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Event> $mapper */
        $mapper = $this->orm->mapper(Event::class);

        /** @var EntityMapper<EventStage> $stageMapper */
        $stageMapper = $this->orm->mapper(EventStage::class);

        /** @var EntityMapper<EventPlan> $planMapper */
        $planMapper = $this->orm->mapper(EventPlan::class);

        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'event'])->dump();
        $venueIds = $this->orm->findColumn(Venue::class, 'id')->dump();

        $createImage = function (int $w = 1200, int $h = 800) use ($faker) {
            return ['url' => $faker->unsplashImage($w, $h)];
        };

        foreach (range(1, 30) as $i) {
            $currentDate = Chronos::wrap($faker->dateTimeThisYear());

            $item = $mapper->createEntity();

            $item->categoryId = (int) $faker->randomElement($categoryIds);
            $item->title = $faker->sentence(2);
            $item->alias = SlugHelper::safe($item->title);
            $item->subtitle = $faker->sentence(3);
            $item->cover = $faker->unsplashImage(1200, 800);
            $item->images = [
                $createImage(),
                $createImage(),
                $createImage(),
            ];
            $item->intro = $faker->sentence(5);
            $item->description = $faker->paragraph(3);
            $item->state = BasicState::PUBLISHED;

            $event = $mapper->createOne($item);

            foreach (range(1, 4) as $s) {
                $stage = $stageMapper->createEntity();

                $stage->eventId = $event->id;
                $stage->venueId = (int) $faker->randomElement($venueIds);
                $stage->title = 'Stage ' . $s;
                $stage->cover = $faker->unsplashImage(1200, 800);
                $stage->images = [
                    $createImage(),
                    $createImage(),
                    $createImage(),
                ];
                $stage->description = $faker->paragraph(3);
                $stage->quota = 20;
                $stage->alternate = 5;
                $stage->less = 5;
                $stage->state = BasicState::PUBLISHED;
                $stage->ordering = $s;
                $stage->startDate = $currentDate = $currentDate->modify('+2months');
                $stage->startDate = Chronos::wrap($stage->startDate);
                $stage->endDate = Chronos::wrap($currentDate->modify('+14days'));

                $stage = $stageMapper->createOne($stage);

                // Plans
                $plan = new EventPlan();
                $plan->eventId = $event->id;
                $plan->stageId = $stage->id;
                $plan->title = 'Early Access';
                $plan->endDate = Chronos::wrap($stage->startDate->modify('-30days'));
                $plan->state = BasicState::PUBLISHED;
                $plan->quota = 5;
                $plan->onceMax = 1;
                $plan->originPrice = 800;
                $plan->price = 500;

                $planMapper->createOne($plan);

                $plan = new EventPlan();
                $plan->eventId = $event->id;
                $plan->stageId = $stage->id;
                $plan->title = 'Basic Ticket';
                $plan->startDate = Chronos::wrap($stage->startDate->modify('-30days'));
                $plan->state = BasicState::PUBLISHED;
                $plan->quota = $stage->quota;
                $plan->onceMax = 1;
                $plan->price = 800;

                $planMapper->createOne($plan);

                $this->printCounting();
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Event::class, EventStage::class, EventPlan::class);
    }
};
