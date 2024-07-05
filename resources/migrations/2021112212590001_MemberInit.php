<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2021.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\Member\Migration;

use Lyrasoft\Member\Entity\Member;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021112212590001_MemberInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Member::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('name');
                $schema->varchar('alias');
                $schema->varchar('image');
                $schema->longtext('intro');
                $schema->longtext('description');
                $schema->varchar('job_title');
                $schema->varchar('email');
                $schema->varchar('phone');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('category_id');
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
        $mig->dropTables(Member::class);
    }
);
