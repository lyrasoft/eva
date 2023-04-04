<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Location;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;
use Windwalker\ORM\NestedSetMapper;
use Windwalker\ORM\ORM;

use function Windwalker\fs;

/**
 * Migration UP: 2022122708280011_LocationInit.
 *
 * @var Migration $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function (ORM $orm) use ($mig, &$importLocations) {
        $mig->createTable(
            Location::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->integer('parent_id');
                $schema->char('type')->comment('LocationType: continent, country, state, city');
                $schema->integer('lft');
                $schema->integer('rgt');
                $schema->integer('level');
                $schema->varchar('region');
                $schema->varchar('subregion');
                $schema->varchar('title');
                $schema->varchar('native');
                $schema->char('code')->length(32);
                $schema->char('code3')->length(3);
                $schema->text('address_format');
                $schema->bool('postcode_required');
                $schema->bool('has_states');
                $schema->bool('can_ship');
                $schema->varchar('call_prefix');
                $schema->bool('state');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
                $schema->addIndex('parent_id');
                $schema->addIndex('type');
                $schema->addIndex('lft');
                $schema->addIndex('rgt');
                $schema->addIndex('level');
                $schema->addIndex('code');
                $schema->addIndex('code3');
            }
        );

        /** @var NestedSetMapper $mapper */
        // $mapper = $orm->mapper(Location::class);
        // $mapper->createRootIfNotExist(
        //     [
        //         'type' => LocationType::ROOT(),
        //     ]
        // );

        $importLocations($orm);
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Location::class);
    }
);

$importLocations = static function (ORM $orm) use ($mig, $app) {
    /** @var NestedSetMapper<Location> $mapper */
    $mapper = $orm->mapper(Location::class);

    $lines = fs(__DIR__ . '/data/locations.csv')
        ->read()
        ->explode("\n")
        ->filter('strlen');

    $keys = str_getcsv((string) $lines->shift());
    $locations = $lines->map(
        static fn(string $line) => array_combine($keys, str_getcsv($line))
    );

    foreach ($locations->chunk(500) as $chunk) {
        $mapper->insert()
            ->columns(...$keys)
            ->values(...$chunk)
            ->execute();
    }
};
