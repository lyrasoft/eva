<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\EventBooking\Entity\Event;
use Lyrasoft\EventBooking\Entity\EventAttend;
use Lyrasoft\EventBooking\Entity\EventMemberMap;
use Lyrasoft\EventBooking\Entity\EventOrder;
use Lyrasoft\EventBooking\Entity\EventPlan;
use Lyrasoft\EventBooking\Entity\EventStage;
use Lyrasoft\EventBooking\Entity\Venue;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024070509030001_EventInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Event::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->integer('last_stage_id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->varchar('subtitle');
                $schema->varchar('cover');
                $schema->json('images')->nullable(true);
                $schema->longtext('intro');
                $schema->longtext('description');
                $schema->bool('state');
                $schema->datetime('publish_up');
                $schema->datetime('start_date');
                $schema->datetime('end_date');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
                $schema->addIndex('last_stage_id');
            }
        );

        $mig->createTable(
            EventStage::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('event_id');
                $schema->integer('venue_id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->varchar('cover');
                $schema->json('images');
                $schema->longtext('description');
                $schema->varchar('attend_url');
                $schema->integer('quota')->nullable(true);
                $schema->integer('alternate')->nullable(true);
                $schema->integer('less')->nullable(true);
                $schema->integer('attends');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('publish_up');
                $schema->datetime('start_date');
                $schema->datetime('end_date');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('event_id');
                $schema->addIndex('venue_id');
            }
        );

        $mig->createTable(
            EventMemberMap::class,
            function (Schema $schema) {
                $schema->varchar('type');
                $schema->integer('member_id');
                $schema->integer('target_id');

                $schema->addIndex('type');
                $schema->addIndex('member_id');
                $schema->addIndex('target_id');
            }
        );

        $mig->createTable(
            EventPlan::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('event_id');
                $schema->integer('stage_id');
                $schema->varchar('title');
                $schema->decimal('price');
                $schema->decimal('origin_price')->nullable(true);
                $schema->bool('state');
                $schema->datetime('start_date');
                $schema->datetime('end_date');
                $schema->bool('require_validate');
                $schema->integer('quota');
                $schema->integer('sold');
                $schema->integer('once_max');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('event_id');
                $schema->addIndex('stage_id');
            }
        );

        $mig->createTable(
            EventOrder::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('event_id');
                $schema->integer('stage_id');
                $schema->varchar('no');
                $schema->varchar('transaction_no');
                $schema->varchar('invoice_type');
                $schema->json('invoice_data')->nullable(true);
                $schema->decimal('total');
                $schema->json('totals')->nullable(true);
                $schema->varchar('name');
                $schema->varchar('email');
                $schema->varchar('nick');
                $schema->varchar('mobile');
                $schema->varchar('phone');
                $schema->varchar('address');
                $schema->json('details')->nullable(true);
                $schema->json('histories')->nullable(true);
                $schema->varchar('state')->comment('OrderState: pending, paid, done');
                $schema->integer('attends');
                $schema->integer('alternates');
                $schema->varchar('payment');
                $schema->json('payment_data');
                $schema->datetime('expired_at');
                $schema->datetime('paid_at');
                $schema->datetime('done_at');
                $schema->json('screenshots')->nullable(true);
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('no');
                $schema->addIndex('user_id');
                $schema->addIndex('event_id');
                $schema->addIndex('stage_id');
            }
        );

        $mig->createTable(
            EventAttend::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('order_id');
                $schema->integer('user_id');
                $schema->integer('event_id');
                $schema->integer('stage_id');
                $schema->integer('plan_id');
                $schema->varchar('plan_title');
                $schema->varchar('no');
                $schema->decimal('price');
                $schema->varchar('name');
                $schema->varchar('email');
                $schema->varchar('nick');
                $schema->varchar('mobile');
                $schema->varchar('phone');
                $schema->varchar('address');
                $schema->json('details')->nullable(true);
                $schema->json('screenshots')->nullable(true);
                $schema->varchar('state')->comment('AttendState: pending, checked_in');
                $schema->datetime('checked_in_at');
                $schema->bool('alternate');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('no');
                $schema->addIndex('order_id');
                $schema->addIndex('user_id');
                $schema->addIndex('event_id');
                $schema->addIndex('stage_id');
                $schema->addIndex('plan_id');
            }
        );

        $mig->createTable(
            Venue::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('title');
                $schema->varchar('url');
                $schema->varchar('address');
                $schema->varchar('map_url');
                $schema->varchar('image');
                $schema->longtext('description');
                $schema->json('links')->nullable(true);
                $schema->longtext('note');
                $schema->bool('state');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Event::class);
        $mig->dropTables(EventStage::class);
        $mig->dropTables(EventMemberMap::class);
        $mig->dropTables(EventPlan::class);
        $mig->dropTables(EventOrder::class);
        $mig->dropTables(EventAttend::class);
        $mig->dropTables(Venue::class);
    }
);
