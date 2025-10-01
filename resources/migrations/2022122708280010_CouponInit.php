<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Coupon;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280010_CouponInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Coupon::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('discount_id');
                $schema->integer('user_id');
                $schema->varchar('code');
                $schema->bool('used');
                $schema->datetime('used_at');

                $schema->addIndex('discount_id');
                $schema->addIndex('user_id');
                $schema->addIndex('code');
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
        $mig->dropTables(Coupon::class);
    }
);
