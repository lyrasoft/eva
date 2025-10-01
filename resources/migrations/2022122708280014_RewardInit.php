<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\RewardHistory;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280014_RewardInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            RewardHistory::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('order_id');
                $schema->char('action')->length(5)->comment('RewardAction: plus,minus,use');
                $schema->decimal('points')->length('20,4');
                $schema->decimal('remain')->length('20,4');
                $schema->varchar('ratio');
                $schema->datetime('time');
                $schema->text('note');
                $schema->integer('created_by');

                $schema->addIndex('user_id');
                $schema->addIndex('order_id');
                $schema->addIndex('action');
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
        $mig->dropTables(RewardHistory::class);
    }
);
