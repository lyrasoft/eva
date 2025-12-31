<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2023.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Melo\Entity\Question;
use Lyrasoft\Melo\Entity\UserAnswer;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2023121810510006_QuestionInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Question::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('lesson_id');
                $schema->integer('segment_id');
                $schema->varchar('type');
                $schema->bool('is_multiple');
                $schema->varchar('title');
                $schema->longtext('content');
                $schema->longtext('answer');
                $schema->varchar('image');
                $schema->integer('score');
                $schema->tinyint('state');
                $schema->integer('ordering');
                $schema->datetime('created')->nullable(true);
                $schema->datetime('modified')->nullable(true);
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('lesson_id');
                $schema->addIndex('segment_id');
            }
        );

        $mig->createTable(
            UserAnswer::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('lesson_id');
                $schema->integer('question_id');
                $schema->varchar('question_type');
                $schema->integer('score');
                $schema->json('value')->nullable(true);
                $schema->bool('is_correct');
                $schema->datetime('created')->nullable(true);

                $schema->addIndex('user_id');
                $schema->addIndex('lesson_id');
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
        $mig->dropTables(
            Question::class,
            UserAnswer::class,
        );
    }
);
