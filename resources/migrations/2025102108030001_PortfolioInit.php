<?php

declare(strict_types=1);

namespace Lyrasoft\Portfolio\Migration;

use Lyrasoft\Portfolio\Entity\Portfolio;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2025102108030001_PortfolioInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Portfolio::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->varchar('subtitle');
                $schema->longtext('description');
                $schema->varchar('cover');
                $schema->json('images');
                $schema->varchar('url');
                $schema->json('meta');
                $schema->tinyint('state')->length(1)->comment('0: unpublished, 1:published');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('category_id');
                $schema->addIndex('ordering');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Portfolio::class);
    }
};
