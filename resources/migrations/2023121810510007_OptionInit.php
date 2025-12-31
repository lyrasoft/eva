<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2023.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Melo\Entity\Option;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2023121810510007_OptionInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Option::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('question_id');
                $schema->varchar('title');
                $schema->bool('is_answer');
                $schema->tinyint('state');
                $schema->integer('ordering');
                $schema->json('params')->nullable(true);

                $schema->addIndex('question_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Option::class);
    }
);
