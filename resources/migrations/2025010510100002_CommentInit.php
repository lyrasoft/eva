<?php

declare(strict_types=1);

namespace App\Migration;

use App\Entity\Comment;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2025010510100002_CommentInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
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
                $schema->decimal('rating');
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
                $schema->addIndex('type');
                $schema->addIndex('rating');
                $schema->addIndex('created_by');
                $schema->addIndex('email');
                $schema->addIndex('reply_user_id');
                $schema->addIndex('last_reply_at');
                $schema->addIndex('last_reply_id');
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
        $mig->dropTables(Comment::class);
    }
);
