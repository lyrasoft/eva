<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Sequence\Entity\Sequence;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022030307270001_SequenceInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Sequence::class,
            function (Schema $schema) {
                $schema->varchar('type');
                $schema->varchar('prefix');
                $schema->integer('serial');

                $schema->addPrimaryKey(['type', 'prefix']);
                $schema->addIndex('type');
                $schema->addIndex('prefix');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Sequence::class);
    }
);
