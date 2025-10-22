<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderHistory;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Entity\OrderState;
use Lyrasoft\ShopGo\Entity\OrderTotal;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;
use Windwalker\ORM\ORM;

use function Windwalker\fs;

return new /** 2022122708280005_OrderInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(ORM $orm, ShopGoPackage $shopGo): void
    {
        $this->createTable(
            Order::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->varchar('no');
                $schema->decimal('total')->length('20,4');
                $schema->decimal('rewards')->length('20,4');
                $schema->char('invoice_type')->length(7)->comment('InvoiceType: idv,company');
                $schema->varchar('invoice_no');
                $schema->json('invoice_data')->nullable(true);
                $schema->integer('state_id');
                $schema->varchar('state_text');
                $schema->varchar('payment_id');
                $schema->varchar('payment_no');
                $schema->json('payment_data')->nullable(true);
                $schema->json('payment_args')->nullable(true);
                $schema->json('payment_info')->nullable(true);
                $schema->varchar('shipping_id');
                $schema->varchar('shipping_no');
                $schema->varchar('shipping_status');
                $schema->json('shipping_data')->nullable(true);
                $schema->json('shipping_args')->nullable(true);
                $schema->json('shipping_info')->nullable(true);
                $schema->json('shipping_history')->nullable(true);
                $schema->longtext('note');
                $schema->datetime('paid_at');
                $schema->datetime('shipped_at');
                $schema->datetime('returned_at');
                $schema->datetime('done_at');
                $schema->datetime('cancelled_at');
                $schema->datetime('rollback_at');
                $schema->datetime('expiry_on');
                $schema->longtext('search_index');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('user_id');
                $schema->addUniqueKey('no');
                $schema->addIndex('state_id');
                // $schema->addIndex('invoice_data');
                $schema->addIndex('payment_no');
                $schema->addIndex('shipping_no');
            }
        );
        $this->createTable(
            OrderItem::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('parent_id');
                $schema->integer('order_id');
                $schema->integer('product_id');
                $schema->integer('variant_id');
                $schema->integer('attachment_id');
                $schema->varchar('variant_hash');
                $schema->varchar('key');
                $schema->varchar('title');
                $schema->varchar('variant_title');
                $schema->varchar('image');
                $schema->json('product_data')->nullable(true);
                $schema->integer('quantity');
                $schema->decimal('price_unit')->length('20,4');
                $schema->decimal('base_price_unit')->length('20,4');
                $schema->decimal('total')->length('20,4');
                $schema->json('price_set');
                $schema->json('options')->nullable(true);
                $schema->json('params')->nullable(true);

                $schema->addIndex('parent_id');
                $schema->addIndex('order_id');
                $schema->addIndex('product_id');
                $schema->addIndex('variant_id');
                $schema->addIndex('variant_hash');
                $schema->addIndex('attachment_id');
            }
        );
        $this->createTable(
            OrderTotal::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->integer('discount_id');
                $schema->varchar('discount_type')->length(20);
                $schema->varchar('type')->length(10)->comment('OrderTotalType: total,discount');
                $schema->varchar('title');
                $schema->varchar('code');
                $schema->decimal('value')->length('20,4');
                $schema->integer('ordering');
                $schema->bool('protect');
                $schema->json('params')->nullable(true);

                $schema->addIndex('order_id');
                $schema->addIndex('discount_id');
                $schema->addIndex('discount_type');
                $schema->addIndex('code');
                $schema->addIndex('ordering');
            }
        );
        $this->createTable(
            OrderHistory::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->char('type')->comment('OrderHistoryType: member,admin,system');
                $schema->integer('state_id');
                $schema->varchar('state_text');
                $schema->varchar('state_color');
                $schema->bool('notify');
                $schema->longtext('message');
                $schema->datetime('created');
                $schema->integer('created_by');

                $schema->addIndex('order_id');
                $schema->addIndex('state_id');
            }
        );
        $this->createTable(
            OrderState::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->bool('default');
                $schema->varchar('color');
                $schema->varchar('image');
                $schema->bool('notice');
                $schema->bool('attach_invoice');
                $schema->bool('shipped');
                $schema->bool('paid');
                $schema->bool('returned');
                $schema->bool('done');
                $schema->bool('cancel');
                $schema->bool('rollback');
                $schema->integer('ordering');

                $schema->addIndex('ordering');
                $schema->addIndex('alias');
            }
        );

        // Create default states
        $states = fs(__DIR__ . '/data/order-states.json')
            ->read()
            ->jsonDecode();

        $locale = $shopGo->config('fixtures.locale') ?: 'en_US';

        foreach ($states as $i => $state) {
            $state['ordering'] = $i + 1;

            $localeTitle = 'title:' . $locale;

            if (isset($state[$localeTitle])) {
                $state['title'] = $state[$localeTitle];
            }

            $orm->createOne(OrderState::class, $state);

            $this->outCounting();
        }
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Order::class);
        $this->dropTables(OrderItem::class);
        $this->dropTables(OrderTotal::class);
        $this->dropTables(OrderHistory::class);
        $this->dropTables(OrderState::class);
    }
};
