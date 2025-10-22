<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280003_ShopInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            ShopCategoryMap::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type')->comment('No enum - product,attribute,tab');
                $schema->integer('target_id');
                $schema->integer('category_id');
                $schema->bool('primary');
                $schema->integer('ordering');

                $schema->addIndex('type');
                $schema->addIndex('target_id');
                $schema->addIndex('category_id');
                $schema->addIndex('ordering');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(ShopCategoryMap::class);
    }
};
