<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Config;
use Lyrasoft\ShopGo\Enum\OrderNoMode;
use Lyrasoft\Toolkit\Encode\BaseConvert;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

return new /** 2023012907410001_ShopGoConfigDefault */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(ORM $orm): void
    {
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

    #[MigrateDown]
    public function down(): void
    {
        //
    }
};
