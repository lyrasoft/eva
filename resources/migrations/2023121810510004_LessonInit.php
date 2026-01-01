<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Melo\Entity\LessonCategoryMap;
use Lyrasoft\Melo\Entity\Lesson;
use Lyrasoft\Melo\Entity\UserLessonMap;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2023121810510004_LessonInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Lesson::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('title');
                $schema->varchar('alias');
                $schema->integer('teacher_id');
                $schema->longtext('description');
                $schema->longtext('acquired')->comment('可以學習到什麼');
                $schema->varchar('image');
                $schema->tinyint('state');
                $schema->datetime('start_date')->nullable(true);
                $schema->datetime('end_date')->nullable(true);
                $schema->bool('is_step_by_step');
                $schema->bool('has_certificate');
                $schema->decimal('price');
                $schema->decimal('special_price');
                $schema->bool('is_special');
                $schema->bool('is_free');
                $schema->float('pass_average_score');
                $schema->float('pass_min_score');
                $schema->integer('ordering');
                $schema->datetime('created')->nullable(true);
                $schema->datetime('modified')->nullable(true);
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('category_id');
                $schema->addIndex('teacher_id');
            }
        );

        $this->createTable(
            LessonCategoryMap::class,
            function (Schema $schema) {
                $schema->integer('lesson_id');
                $schema->integer('category_id');
                $schema->bool('is_primary');

                $schema->addIndex('lesson_id');
                $schema->addIndex('category_id');
            }
        );

        $this->createTable(
            UserLessonMap::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('lesson_id');
                $schema->char('status')->length(7)->comment('UserLessonStatus: pass, fail, process');
                $schema->datetime('created')->nullable(true);

                $schema->addIndex('user_id');
                $schema->addIndex('lesson_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(
            Lesson::class,
            LessonCategoryMap::class,
            UserLessonMap::class
        );
    }
};
