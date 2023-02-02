<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Manufacturer;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280001_ManufacturerInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Manufacturer::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->varchar('image');
                $schema->longtext('introtext');
                $schema->integer('page_id');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->json('meta')->nullable(true);
                $schema->longtext('search_index');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->nullable(true);

                $schema->addIndex('alias');
                $schema->addIndex('page_id');
                $schema->addIndex('language');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        // $mig->dropTableColumns(Table::class, 'column');
        $mig->dropTables(Manufacturer::class);
    }
);
