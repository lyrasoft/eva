<?php

declare(strict_types=1);

namespace Lyrasoft\Formkit\Migration;

use Lyrasoft\Formkit\Entity\Formkit;
use Lyrasoft\Formkit\Entity\FormkitResponse;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024081707520001_FormkitInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Formkit::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alias')->comment('Alias');
                $schema->longtext('description')->comment('Desc');
                $schema->json('content')->comment('Content');
                $schema->varchar('image')->comment('Main Image');
                $schema->varchar('extends')->comment('Extends');
                $schema->bool('state');
                $schema->bool('public');
                $schema->datetime('publish_up');
                $schema->datetime('publish_down');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('publish_up');
                $schema->addIndex('publish_down');
                $schema->addIndex('language');
                $schema->addIndex('created_by');
            }
        );
        $mig->createTable(
            FormkitResponse::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->integer('formkit_id');
                $schema->json('content');
                $schema->varchar('state');
                $schema->varchar('from')->length(512);
                $schema->varchar('ip');
                $schema->varchar('ua');
                $schema->varchar('browser');
                $schema->varchar('os');
                $schema->varchar('device');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params');

                $schema->addIndex('formkit_id');
                $schema->addIndex('created_by');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Formkit::class, FormkitResponse::class);
    }
);
