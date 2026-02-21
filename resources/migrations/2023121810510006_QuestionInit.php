<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Melo\Entity\Question;
use Lyrasoft\Melo\Entity\UserAnswer;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2023121810510006_QuestionInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Question::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('lesson_id');
                $schema->integer('segment_id');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->longtext('content');
                $schema->longtext('answer');
                $schema->varchar('image');
                $schema->decimal('score')->length('10,2');
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

        $this->createTable(
            UserAnswer::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('lesson_id');
                $schema->integer('quiz_id');
                $schema->integer('question_id');
                $schema->varchar('question_type');
                $schema->decimal('score')->length('10,2');
                $schema->json('value')->nullable(true);
                $schema->bool('is_correct');
                $schema->datetime('created')->nullable(true);

                $schema->addIndex('user_id');
                $schema->addIndex('lesson_id');
                $schema->addIndex('quiz_id');
                $schema->addIndex('question_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(
            Question::class,
            UserAnswer::class,
        );
    }
};
