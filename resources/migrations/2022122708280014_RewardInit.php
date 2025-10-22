<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\RewardHistory;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280014_RewardInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            RewardHistory::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('order_id');
                $schema->char('action')->length(5)->comment('RewardAction: plus,minus,use');
                $schema->decimal('points')->length('20,4');
                $schema->decimal('remain')->length('20,4');
                $schema->varchar('ratio');
                $schema->datetime('time');
                $schema->text('note');
                $schema->integer('created_by');

                $schema->addIndex('user_id');
                $schema->addIndex('order_id');
                $schema->addIndex('action');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(RewardHistory::class);
    }
};
