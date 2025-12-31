<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2023.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Melo\Migration;

use Lyrasoft\Melo\Entity\OrderHistory;
use Lyrasoft\Melo\Entity\OrderItem;
use Lyrasoft\Melo\Entity\Order;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2023121810510008_OrderInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Order::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->varchar('no');
                $schema->decimal('total')->length('20,4');
                $schema->varchar('invoice_no');
                $schema->char('invoice_type')->length(7)->comment('InvoiceType: idv,company');
                $schema->json('invoice_data')->nullable(true);
                $schema->varchar('state');
                $schema->varchar('payment');
                $schema->varchar('payment_no');
                $schema->json('payment_data')->nullable(true);
                $schema->longtext('note');
                $schema->datetime('paid_at')->nullable(true);
                $schema->datetime('cancelled_at')->nullable(true);
                $schema->datetime('expired_on')->nullable(true);
                $schema->longtext('search_index');
                $schema->datetime('created')->nullable(true);
                $schema->datetime('modified')->nullable(true);
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('user_id');

                $schema->addUniqueKey('no');
                $schema->addIndex('payment_no');
            }
        );
        $mig->createTable(
            OrderItem::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->integer('lesson_id');
                $schema->varchar('title');
                $schema->varchar('image');
                $schema->json('lesson_data')->nullable(true);
                $schema->decimal('price')->length('20,4');
                $schema->decimal('total')->length('20,4');
                $schema->json('price_set');
                $schema->json('options')->nullable(true);
                $schema->json('params')->nullable(true);

                $schema->addIndex('order_id');
                $schema->addIndex('lesson_id');
            }
        );
        $mig->createTable(
            OrderHistory::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->char('type')->length(7)->comment('OrderHistoryType: member,admin,system');
                $schema->varchar('state');
                $schema->bool('notify');
                $schema->longtext('message');
                $schema->datetime('created')->nullable(true);
                $schema->integer('created_by');

                $schema->addIndex('order_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Order::class);
        $mig->dropTables(OrderItem::class);
        $mig->dropTables(OrderHistory::class);
    }
);
