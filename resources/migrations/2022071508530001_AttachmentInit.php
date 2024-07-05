<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use App\Entity\Attachment;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022071508530001_AttachmentInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $table = $mig->getTable(Attachment::class);

        if (!$table->exists()) {
            $mig->createTable(
                Attachment::class,
                function (Schema $schema) {
                    $schema->primary('id')->comment('ID');
                    $schema->varchar('type')->comment('類型');
                    $schema->integer('target_id')->comment('目標 ID');
                    $schema->varchar('title')->comment('檔案名稱');
                    $schema->varchar('alt')->comment('Alt');
                    $schema->integer('size')->comment('檔案大小');
                    $schema->varchar('mime')->comment('媒體類型');
                    $schema->varchar('path')->comment('路徑');
                    $schema->text('description')->comment('內容');
                    $schema->integer('ordering')->comment('Ordering');
                    $schema->json('params')->comment('Params');

                    $schema->addIndex('target_id');
                }
            );
        }
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Attachment::class);
    }
);
