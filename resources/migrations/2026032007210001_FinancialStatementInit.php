<?php

declare(strict_types=1);

namespace App\Migration;

use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2026032007210001_FinancialStatementInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        // $this->createTable(
        //     \App\Entity\FinancialStatement::class,
        //     function (Schema $schema) {}
        // );
    }

    #[MigrateDown]
    public function down(): void
    {
        // $this->dropTables(\App\Entity\FinancialStatement::class);
    }
};
