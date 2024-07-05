<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Event;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * EventOrder Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        $events = $orm->findList(Event::class)
            ->all()
            ->keyBy('id')
            ->dump();

        $stages = $orm->findList(EventStage::class)
            ->all()
            ->dump();

        /** @var EntityMapper<EventOrder> $mapper */
        $mapper = $orm->mapper(EventOrder::class);

        foreach (range(1, 50) as $i) {
            /** @var EventStage $stage */
            $stage = $faker->randomElement($stages);
            /** @var Event $event */
            $event = $events[$stage->getEventId()];

            $item = $mapper->createEntity();

            $item->setEventId($event->getId());
            $item->setStageId($stage->getId());
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);
