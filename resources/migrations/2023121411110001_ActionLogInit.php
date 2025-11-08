<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ActionLog\Entity\ActionLog;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2023121411110001_ActionLogInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            ActionLog::class,
            function (Schema $schema) {
                $schema->primaryBigint('id');
                $schema->varchar('session_id');
                $schema->varchar('user_id');
                $schema->varchar('email');
                $schema->varchar('username');
                $schema->varchar('name');
                $schema->varchar('ip');
                $schema->varchar('device');
                $schema->varchar('ua')->length(512);
                $schema->varchar('referrer')->length(1024);
                $schema->varchar('url')->length(1024);
                $schema->varchar('stage');
                $schema->varchar('route');
                $schema->varchar('controller');
                $schema->integer('status');
                $schema->char('method')->length(10);
                $schema->varchar('task');
                $schema->text('ids');
                $schema->json('body');
                $schema->datetime('time');

                $schema->addIndex('user_id');
                $schema->addIndex('email');
                $schema->addIndex('username');
                $schema->addIndex('time');
                $schema->addIndex('task');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(ActionLog::class);
    }
);
