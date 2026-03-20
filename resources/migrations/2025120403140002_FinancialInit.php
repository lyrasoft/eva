<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Investor\Entity\FinancialStatement;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2025120403140002_FinancialInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            FinancialStatement::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title')->comment('年度');
                $schema->varchar('alias');
                $schema->varchar('q1_consolidated')->comment('第一季合併財報檔案');
                $schema->varchar('q2_consolidated')->comment('第二季合併財報檔案');
                $schema->varchar('q3_consolidated')->comment('第三季合併財報檔案');
                $schema->varchar('q4_consolidated')->comment('第四季合併財報檔案');
                $schema->varchar('q4_individual')->comment('第四季個體財報檔案');
                $schema->json('monthly_report')->nullable(true)->comment('每月營收報告');
                $schema->varchar('annual_report')->comment('年報檔案');
                $schema->json('earnings_call_files')->nullable(true)->comment('法人說明會檔案');
                $schema->tinyint('state');
                $schema->char('language')->length(7);
                $schema->integer('ordering');
                $schema->datetime('created')->nullable(true);
                $schema->datetime('modified')->nullable(true);
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('alias');
                $schema->addIndex('created');
                $schema->addIndex('ordering');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        // $this->dropTables(Table::class);
        $this->dropTables(FinancialStatement::class);
    }
};
