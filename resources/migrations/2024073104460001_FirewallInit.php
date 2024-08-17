<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Firewall\Entity\IpRule;
use Lyrasoft\Firewall\Entity\Redirect;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024073104460001_FirewallInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Redirect::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type');
                $schema->varchar('src')->length(1024);
                $schema->varchar('dest')->length(1024);
                $schema->integer('status');
                $schema->tinyint('state')->length(1);
                $schema->integer('ordering');
                $schema->text('note');
                $schema->integer('hits');
                $schema->datetime('last_hit');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('type');
                $schema->addIndex('src');
                $schema->addIndex('ordering');
            }
        );
        $mig->createTable(
            IpRule::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type');
                $schema->varchar('kind');
                $schema->varchar('range');
                $schema->tinyint('state')->length(1);
                $schema->integer('ordering');
                $schema->text('note');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('type');
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
        $mig->dropTables(Redirect::class, IpRule::class);
    }
);
