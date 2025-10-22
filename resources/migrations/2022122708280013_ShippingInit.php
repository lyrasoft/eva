<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Shipping;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280013_ShippingInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
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
                $schema->json('allow_tags')->nullable(true);
                $schema->json('unallow_tags')->nullable(true);
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

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Shipping::class);
    }
};
