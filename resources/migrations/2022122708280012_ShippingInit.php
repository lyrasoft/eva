<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Shipping;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280012_ShippingInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Shipping::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('location_category_id');
                $schema->integer('location_id');
                $schema->varchar('classname');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->varchar('subtitle');
                $schema->varchar('alias');
                $schema->longtext('description');
                $schema->varchar('note');
                $schema->varchar('image');
                $schema->json('payments')->nullable(true);
                $schema->json('tags')->nullable(true);
                $schema->json('pricing')->nullable(true);
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('location_category_id');
                $schema->addIndex('location_id');
                $schema->addIndex('type');
                $schema->addIndex('alias');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Shipping::class);
    }
);
