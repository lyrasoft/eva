<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Melo\Entity\Segment;
use Lyrasoft\Melo\Entity\UserSegmentMap;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2023121810510005_SegmentInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Segment::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('lesson_id');
                $schema->integer('parent_id');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->longtext('content');
                $schema->varchar('src');
                $schema->varchar('filename');
                $schema->varchar('ext');
                $schema->varchar('caption_src');
                $schema->integer('duration')->comment('unit is second');
                $schema->bool('can_skip');
                $schema->bool('preview');
                $schema->tinyint('state');
                $schema->integer('ordering');
                $schema->datetime('created')->nullable(true);
                $schema->integer('created_by');
                $schema->datetime('modified')->nullable(true);
                $schema->integer('modified_by');
                $schema->json('params')->nullable(true);

                $schema->addIndex('lesson_id');
                $schema->addIndex('parent_id');
            }
        );

        $this->createTable(
            UserSegmentMap::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('lesson_id');
                $schema->integer('segment_id');
                $schema->varchar('segment_type');
                $schema->char('status')->length(7)->comment('UserSegmentStatus: pass, fail, process, done');
                $schema->longtext('description');
                $schema->integer('score');
                $schema->varchar('assignment');
                $schema->datetime('assignment_upload_time')->nullable(true)->comment('作業上傳時間');
                $schema->bool('front_show');
                $schema->datetime('created')->nullable(true);

                $schema->addIndex('user_id');
                $schema->addIndex('lesson_id');
                $schema->addIndex('segment_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(
            Segment::class,
            UserSegmentMap::class
        );
    }
};
