<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2023.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Enum\OrderNoMode;
use Lyrasoft\Luna\Entity\Config;
use Lyrasoft\Toolkit\Encode\BaseConvert;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Migration UP: 2023012907410001_ShopGoConfigDefault.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function (ORM $orm) use ($mig) {
        /** @var EntityMapper<Config> $mapper */
        $mapper = $orm->mapper(Config::class);

        $item = $mapper->createEntity();

        $item->type = 'shopgo_shop';
        $item->content = [
                'currency_main' => 1,
                'payment_no_maxlength' => '20',
                'order_no_prefix' => 'S',
                'order_no_mode' => OrderNoMode::INCREMENT_ID,
                'order_hash_offsets' => 100000,
                'sequence_day_format' => 'ymd',
                'order_hash_seed' => str_shuffle(BaseConvert::BASE62),
                'invoice_no_prefix' => 'INV',
            ];

        $mapper->createOne($item);
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        //
    }
);
