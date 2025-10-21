<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Currency;
use Lyrasoft\ShopGo\Enum\SignPosition;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Migration UP: 2022122708280007_CurrencyInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function (ORM $orm) use ($mig) {
        $mig->createTable(
            Currency::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('title');
                $schema->char('code')->length(10);
                $schema->integer('code_num');
                $schema->varchar('sign');
                $schema->char('sign_position')->length(5)->comment('SignPosition: start,end');
                $schema->tinyint('decimal_place');
                $schema->varchar('decimal_point');
                $schema->varchar('num_separator');
                $schema->decimal('exchange_rate')->length('20,4');
                $schema->bool('space');
                $schema->bool('state');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('code');
                $schema->addIndex('code_num');
            }
        );

        // Prepare Default Currencies
        /** @var EntityMapper<Currency> $mapper */
        $mapper = $orm->mapper(Currency::class);

        $item = $mapper->createEntity();

        // USD
        $item->title = 'USD';
        $item->code = 'USD';
        $item->sign = '$';
        $item->signPosition = SignPosition::START;
        $item->decimalPlace = 2;
        $item->decimalPoint = '.';
        $item->numSeparator = ',';
        $item->exchangeRate = 1;
        $item->space = false;
        $item->state = 1;

        $mapper->createOne($item);

        $mig->outCounting();

        // TWD
        $item = $mapper->createEntity();

        $item->title = 'TWD';
        $item->code = 'TWD';
        $item->sign = '$';
        $item->signPosition = SignPosition::START;
        $item->decimalPlace = 0;
        $item->decimalPoint = '.';
        $item->numSeparator = ',';
        $item->exchangeRate = 35;
        $item->space = false;
        $item->state = 1;

        $mapper->createOne($item);

        $mig->outCounting();

        // EUR
        $item = $mapper->createEntity();

        $item->title = 'EUR';
        $item->code = 'EUR';
        $item->sign = '€';
        $item->signPosition = SignPosition::END;
        $item->decimalPlace = 0;
        $item->decimalPoint = ',';
        $item->numSeparator = '.';
        $item->exchangeRate = 0.92;
        $item->space = false;
        $item->state = 1;

        $mapper->createOne($item);

        $mig->outCounting();
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        // $mig->dropTableColumns(Table::class, 'column');
        $mig->dropTables(Currency::class);
    }
);
