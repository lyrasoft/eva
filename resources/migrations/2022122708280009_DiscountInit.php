<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Discount;
use Lyrasoft\ShopGo\Entity\DiscountUsage;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280009_DiscountInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Discount::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('product_id');
                $schema->varchar('type');
                $schema->varchar('subtype')->comment('basic,discount,code,special');
                $schema->varchar('title');
                $schema->decimal('price')->length('20,4')->defaultValue(null)->defaultValue(null);
                $schema->datetime('publish_up');
                $schema->datetime('publish_down');
                $schema->longtext('description');
                $schema->varchar('code');
                $schema->longtext('notice');
                $schema->integer('ordering');
                $schema->bool('state');
                $schema->bool('hide');
                $schema->decimal('min_price')->length('20,4')->nullable(true)->defaultValue(null);
                $schema->integer('quantity')->nullable(true)->defaultValue(null);
                $schema->integer('times_per_user')->nullable(true)->defaultValue(null);
                $schema->integer('first_buy')->nullable(true)->defaultValue(null);
                $schema->integer('after_registered')->nullable(true)->defaultValue(null);
                $schema->bool('can_rollback');
                $schema->char('combine')->length(8)->comment('DiscountCombine: open,stop,includes,excludes');
                $schema->json('combine_targets')->nullable(true)->defaultValue(null);
                $schema->json('users')->nullable(true)->defaultValue(null);
                $schema->json('categories')->nullable(true)->defaultValue(null);
                $schema->json('products')->nullable(true)->defaultValue(null);
                $schema->json('payments')->nullable(true)->defaultValue(null);
                $schema->json('shippings')->nullable(true)->defaultValue(null);
                $schema->json('apply_products')->nullable(true)->defaultValue(null);
                $schema->integer('min_product_quantity')->nullable(true)->defaultValue(null);
                $schema->integer('min_cart_items')->nullable(true)->defaultValue(null);
                $schema->decimal('min_cart_price')->length('20,4')->nullable(true)->defaultValue(null);
                $schema->bool('free_shipping');
                $schema->char('method')->length(10)->comment('DiscountMethod: offset,fixed,percentage');
                $schema->char('apply_to')->length(8)->comment('DiscountApplyTo: order,products,matched');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('product_id');
                $schema->addIndex('type');
                $schema->addIndex('subtype');
                $schema->addIndex('publish_up');
                $schema->addIndex('publish_down');
                $schema->addIndex('code');
                $schema->addIndex('ordering');
            }
        );
        $mig->createTable(
            DiscountUsage::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('discount_id');
                $schema->integer('order_id');
                $schema->varchar('type');
                $schema->integer('user_id');
                $schema->datetime('used_at');
                $schema->json('params')->nullable(true);

                $schema->addIndex('discount_id');
                $schema->addIndex('order_id');
                $schema->addIndex('type');
                $schema->addIndex('user_id');
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
        $mig->dropTables(Discount::class);
        $mig->dropTables(DiscountUsage::class);
    }
);
