<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Address;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022122708280006_AddressInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Address::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('location_id');
                $schema->varchar('firstname');
                $schema->varchar('lastname');
                $schema->varchar('fullname');
                $schema->varchar('email');
                $schema->varchar('phone');
                $schema->varchar('mobile');
                $schema->varchar('company');
                $schema->varchar('country');
                $schema->varchar('state');
                $schema->varchar('city');
                $schema->varchar('postcode');
                $schema->varchar('address1');
                $schema->varchar('address2');
                $schema->varchar('vat');
                $schema->json('details')->nullable(true);
                $schema->bool('enabled');
                $schema->datetime('created');
                $schema->datetime('modified');

                $schema->addIndex('user_id');
                $schema->addIndex('location_id');
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
        $mig->dropTables(Address::class);
    }
);
