<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Feedback\Entity\Comment;
use Lyrasoft\Feedback\Entity\Rating;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2025010510100001_CommentRatingInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Rating::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('target_id');
                $schema->integer('user_id');
                $schema->varchar('type');
                $schema->decimal('float')->length('10,2');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->json('params');

                $schema->addIndex('target_id');
                $schema->addIndex('user_id');
                $schema->addIndex('ordering');
            }
        );

        $this->createTable(
            Comment::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('parent_id');
                $schema->integer('target_id');
                $schema->integer('user_id');
                $schema->varchar('type');
                $schema->varchar('title');
                $schema->longtext('content');
                $schema->varchar('avatar');
                $schema->varchar('nickname');
                $schema->varchar('email');
                $schema->longtext('reply');
                $schema->integer('reply_user_id');
                $schema->datetime('last_reply_at');
                $schema->integer('last_reply_id');
                $schema->decimal('rating')->length('10,2');
                $schema->bool('state');
                $schema->integer('ordering');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('parent_id');
                $schema->addIndex('target_id');
                $schema->addIndex('user_id');
                $schema->addIndex('email');
                $schema->addIndex('reply_user_id');
                $schema->addIndex('last_reply_id');
                $schema->addIndex('last_reply_at');
                $schema->addIndex('rating');
                $schema->addIndex('ordering');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Rating::class, Comment::class);
    }
};
