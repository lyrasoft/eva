<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Investor\Entity\DividendHistory;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2025120403140004_DividendHistoryInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            DividendHistory::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->decimal('stock_dividend')->length('20,8');
                $schema->decimal('cash_dividend')->length('20,8');
                $schema->datetime('ex_dividend_date');
                $schema->datetime('record_date');
                $schema->datetime('payment_date');
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
        $this->dropTables(DividendHistory::class);
    }
};
