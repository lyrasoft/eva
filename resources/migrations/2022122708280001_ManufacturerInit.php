<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Manufacturer;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280001_ManufacturerInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
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

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Manufacturer::class);
    }
};
