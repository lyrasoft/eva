<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Coupon;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280010_CouponInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Coupon::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('discount_id');
                $schema->integer('user_id');
                $schema->varchar('code');
                $schema->bool('used');
                $schema->datetime('used_at');

                $schema->addIndex('discount_id');
                $schema->addIndex('user_id');
                $schema->addIndex('code');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Coupon::class);
    }
};
