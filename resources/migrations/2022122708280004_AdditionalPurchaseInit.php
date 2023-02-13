<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\AdditionalPurchase;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseMap;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280004_AdditionalInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            AdditionalPurchase::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->integer('attach_product_id');
                $schema->integer('attach_variant_id');
                $schema->decimal('price')->length('20,4');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params');

                $schema->addIndex('attach_product_id');
                $schema->addIndex('attach_variant_id');
                $schema->addIndex('ordering');
            }
        );
        $mig->createTable(
            AdditionalPurchaseMap::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('additional_purchase_id');
                $schema->integer('attach_product_id');
                $schema->integer('attach_variant_id');
                $schema->integer('target_product_id');

                $schema->addIndex('additional_purchase_id');
                $schema->addIndex('attach_product_id');
                $schema->addIndex('attach_variant_id');
                $schema->addIndex('target_product_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        // $mig->dropTableColumns(Table::class, 'column');
        $mig->dropTables(AdditionalPurchase::class, AdditionalPurchaseMap::class);
    }
);
