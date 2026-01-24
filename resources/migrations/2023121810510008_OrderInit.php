<?php

declare(strict_types=1);

namespace Lyrasoft\Melo\Migration;

use Lyrasoft\Melo\Entity\MeloOrderHistory;
use Lyrasoft\Melo\Entity\MeloOrderItem;
use Lyrasoft\Melo\Entity\MeloOrder;
use Lyrasoft\Melo\Entity\MeloOrderTotal;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2023121810510008_OrderInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            MeloOrder::class,
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
                $schema->json('snapshots');
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

        $this->createTable(
            MeloOrderItem::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->integer('lesson_id');
                $schema->varchar('title');
                $schema->varchar('image');
                $schema->decimal('price')->length('20,4');
                $schema->decimal('total')->length('20,4');
                $schema->json('price_set');
                $schema->json('snapshots');
                $schema->json('options')->nullable(true);
                $schema->json('params')->nullable(true);

                $schema->addIndex('order_id');
                $schema->addIndex('lesson_id');
            }
        );

        $this->createTable(
            MeloOrderHistory::class,
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

        $this->createTable(
            MeloOrderTotal::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->integer('discount_id');
                $schema->varchar('discount_type');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->varchar('code');
                $schema->decimal('value')->length('20,4');
                $schema->integer('ordering');
                $schema->bool('protect');
                $schema->json('params');

                $schema->addIndex('order_id');
                $schema->addIndex('discount_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(
            MeloOrder::class,
            MeloOrderItem::class,
            MeloOrderHistory::class,
            MeloOrderTotal::class
        );
    }
};
