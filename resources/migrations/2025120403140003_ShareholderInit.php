<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Investor\Entity\ShareholderInfo;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2025120403140003_ShareholderInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            ShareholderInfo::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->datetime('agm_date');
                $schema->varchar('agm_location');
                $schema->varchar('agm_video');
                $schema->json('top_shareholders')->nullable(true);
                $schema->json('primary_shareholders')->nullable(true);
                $schema->varchar('primary_shareholder_doc');
                $schema->bool('show_primary');
                $schema->varchar('notice_file')->comment('開會通知');
                $schema->varchar('agenda_file')->comment('議事手冊');
                $schema->varchar('minutes_file')->comment('議事錄');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->char('language')->length(7);
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('ordering');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(ShareholderInfo::class);
    }
};
