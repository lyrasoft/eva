<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Payment;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280012_PaymentInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Payment::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('location_category_id');
                $schema->integer('location_id');
                $schema->integer('order_state_id');
                $schema->varchar('classname');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->varchar('subtitle');
                $schema->varchar('alias');
                $schema->longtext('description');
                $schema->varchar('note');
                $schema->varchar('image');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('location_category_id');
                $schema->addIndex('location_id');
                $schema->addIndex('order_state_id');
                $schema->addIndex('type');
                $schema->addIndex('alias');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Payment::class);
    }
};
