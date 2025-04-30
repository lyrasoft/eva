<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\TokenCoin\Entity\TokenCoinHistory;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024101106240001_PointHistoryInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            TokenCoinHistory::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('ID');
                $schema->varchar('target_id')->comment('Target ID');
                $schema->varchar('order_id')->comment('Order ID');
                $schema->varchar('agent_id')->comment('Agent ID');
                $schema->varchar('type')->comment('Type');
                $schema->varchar('action')->comment('plus,reduce,use');
                $schema->decimal('value')->length('20,4')->comment('Point');
                $schema->decimal('remain')->length('20,4')->comment('Remain');
                $schema->text('note');
                $schema->datetime('date');
                $schema->datetime('created');
                $schema->integer('created_by');
                $schema->json('params');

                $schema->addIndex('target_id');
                $schema->addIndex('order_id');
                $schema->addIndex('agent_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(TokenCoinHistory::class);
    }
);
