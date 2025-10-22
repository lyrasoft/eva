<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\AdditionalPurchase;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseAttachment;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseTarget;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280004_AdditionalInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            AdditionalPurchase::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('publish_up');
                $schema->datetime('publish_down');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params');

                $schema->addIndex('ordering');
            }
        );
        $this->createTable(
            AdditionalPurchaseAttachment::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('additional_purchase_id');
                $schema->integer('product_id');
                $schema->integer('variant_id');
                $schema->char('method')->length(10);
                $schema->decimal('price')->length('20,4');
                $schema->integer('max_quantity');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params');

                $schema->addIndex('additional_purchase_id');
                $schema->addIndex('product_id');
                $schema->addIndex('variant_id');
                $schema->addIndex('ordering');
            }
        );
        $this->createTable(
            AdditionalPurchaseTarget::class,
            function (Schema $schema) {
                $schema->integer('additional_purchase_id');
                $schema->integer('product_id');

                $schema->addIndex('additional_purchase_id');
                $schema->addIndex('product_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(
            AdditionalPurchase::class,
            AdditionalPurchaseAttachment::class,
            AdditionalPurchaseTarget::class,
        );
    }
};
