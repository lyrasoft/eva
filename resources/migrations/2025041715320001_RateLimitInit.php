<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Throttle\Entity\RateLimit;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2025041715320001_RateLimitInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            RateLimit::class,
            function (Schema $schema) {
                $schema->primaryBigint('id');
                $schema->varchar('key');
                $schema->json('payload');
                $schema->datetime('expired_at');
                $schema->json('params');

                $schema->addUniqueKey('key');
                $schema->addIndex('expired_at');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(RateLimit::class);
    }
};
