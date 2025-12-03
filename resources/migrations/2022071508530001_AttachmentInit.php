<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Attachment\Entity\Attachment;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2022071508530001_Attachment */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Attachment::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('ID');
                $schema->varchar('type')->comment('Type');
                $schema->integer('target_id')->comment('Target ID');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alt')->comment('Alt');
                $schema->integer('size')->comment('Size');
                $schema->varchar('mime')->comment('Mime');
                $schema->varchar('path')->comment('Path');
                $schema->text('description')->comment('Description');
                $schema->integer('ordering')->comment('Ordering');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->json('params')->comment('Params');

                $schema->addIndex('target_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Attachment::class);
    }
};
