<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ProductAttributeMap;
use Lyrasoft\ShopGo\Entity\ProductFeature;
use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280002_ProductInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Product::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->integer('primary_variant_id');
                $schema->varchar('model');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->decimal('origin_price')->length('20,4');
                $schema->integer('safe_stock');
                $schema->longtext('intro');
                $schema->longtext('description');
                $schema->json('meta')->nullable(true);
                $schema->bool('can_attach')->comment('是否是加價購商品');
                $schema->integer('variants');
                $schema->integer('ordering');
                $schema->bool('hide');
                $schema->bool('state');
                $schema->longtext('search_index');
                $schema->json('shippings')->nullable(true);
                $schema->datetime('publish_up');
                $schema->datetime('publish_down');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->integer('hits');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
                $schema->addIndex('model');
                $schema->addIndex('alias');
                $schema->addIndex('publish_up');
                $schema->addIndex('publish_down');
            }
        );
        $mig->createTable(
            ProductVariant::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('product_id');
                $schema->varchar('title');
                $schema->varchar('hash');
                $schema->bool('primary');
                $schema->varchar('sku');
                $schema->varchar('upc');
                $schema->varchar('ean');
                $schema->varchar('jan');
                $schema->varchar('isbn');
                $schema->varchar('mpn');
                $schema->integer('stock_quantity');
                $schema->bool('subtract');
                $schema->decimal('price')->length('20,4');
                $schema->json('dimension')->nullable(true);
                $schema->bool('stock_buyable');
                $schema->varchar('out_of_stock_text');
                $schema->varchar('cover');
                $schema->json('images')->nullable(true);
                $schema->json('options')->nullable(true);
                $schema->bool('state');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('product_id');
                $schema->addIndex('hash');
                $schema->addIndex('sku');
            }
        );
        $mig->createTable(
            ProductFeature::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type')->comment('FeatureType: list,color');
                $schema->varchar('title');
                $schema->varchar('default');
                $schema->varchar('note');
                $schema->integer('ordering');
                $schema->bool('state');
                $schema->json('options')->nullable(true);
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('type');
                $schema->addIndex('ordering');
            }
        );
        $mig->createTable(
            ProductAttribute::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('type')->comment('ProductAttributeType: text,list,bool');
                $schema->varchar('title');
                $schema->varchar('key');
                $schema->bool('display');
                $schema->integer('ordering');
                $schema->bool('state');
                $schema->json('options')->nullable(true);
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
                $schema->addIndex('type');
                $schema->addIndex('key');
                $schema->addIndex('ordering');
            }
        );
        $mig->createTable(
            ProductAttributeMap::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('product_id');
                $schema->integer('attribute_id');
                $schema->varchar('key');
                $schema->varchar('value');
                $schema->varchar('locale')->defaultValue('*');

                $schema->addIndex('product_id');
                $schema->addIndex('attribute_id');
                $schema->addIndex('key');
                $schema->addIndex('value');
                $schema->addIndex('locale');
            }
        );
        $mig->createTable(
            ProductTab::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->integer('article_id');
                $schema->integer('page_id');
                $schema->longtext('content');
                $schema->integer('ordering');
                $schema->bool('state');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('article_id');
                $schema->addIndex('page_id');
                $schema->addIndex('ordering');
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
        $mig->dropTables(Product::class);
        $mig->dropTables(ProductVariant::class);
        $mig->dropTables(ProductFeature::class);
        $mig->dropTables(ProductAttribute::class);
        $mig->dropTables(ProductAttributeMap::class);
        $mig->dropTables(ProductTab::class);
    }
);
