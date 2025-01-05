<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2021.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\faq\Migration;

use Lyrasoft\Faq\Entity\Faq;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 20211112105026329690_FaqInit.
 *
 * @var Migration $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Faq::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id')->comment('分類');
                $schema->varchar('title')->comment('標題');
                $schema->varchar('image')->comment('圖片');
                $schema->tinyint('state')->length(1)->comment('啟用');
                $schema->longtext('description')->comment('內容');
                $schema->integer('ordering')->comment('順序');
                $schema->datetime('created')->comment('建立時間');
                $schema->integer('created_by')->comment('修改時間');
                $schema->datetime('modified')->comment('建立者');
                $schema->integer('modified_by')->comment('修改者');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Faq::class);
    }
);
