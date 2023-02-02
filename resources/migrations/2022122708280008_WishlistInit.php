<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Wishlist;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280008_WishlistInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        // $mig->updateTable(
        //     Table::class,
        //     function (Schema $schema) {}
        // );
        $mig->createTable(
            Wishlist::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('uesr_id');
                $schema->integer('product_id');
                $schema->datetime('created');

                $schema->addIndex('uesr_id');
                $schema->addIndex('product_id');
            }
        );
        $mig->createTable(
            Wishlist::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('uesr_id');
                $schema->integer('product_id');
                $schema->datetime('created');

                $schema->addIndex('uesr_id');
                $schema->addIndex('product_id');
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
        $mig->dropTables(Wishlist::class);
        $mig->dropTables(Wishlist::class);
    }
);
