<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Member\Entity\Member;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2025102109140001_MemberInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Member::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('name');
                $schema->varchar('alias');
                $schema->varchar('image');
                $schema->longtext('intro');
                $schema->longtext('description');
                $schema->varchar('job_title');
                $schema->varchar('email');
                $schema->varchar('phone');
                $schema->bool('state');
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
        $this->dropTables(Member::class);
    }
};
