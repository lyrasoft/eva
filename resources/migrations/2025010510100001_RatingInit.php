<?php

declare(strict_types=1);

namespace App\Migration;

use App\Entity\Rating;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2025010510100001_RatingInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Rating::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('target_id');
                $schema->integer('user_id');
                $schema->varchar('type');
                $schema->decimal('rank')->length('20,4');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->json('params');

                $schema->addIndex('target_id');
                $schema->addIndex('user_id');
                $schema->addIndex('type');
                $schema->addIndex('rank');
                $schema->addIndex('ordering');
                $schema->addIndex('created');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Rating::class);
    }
);
