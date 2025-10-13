<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Banner\Entity\Banner;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022073105330001_BannerInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Banner::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('category_id');
                $schema->varchar('type')->length(50);
                $schema->varchar('title');
                $schema->varchar('subtitle');
                $schema->varchar('image');
                $schema->varchar('mobile_image');
                $schema->char('video_type')->length(5)->comment('embed,file');
                $schema->varchar('video');
                $schema->varchar('mobile_video');
                $schema->varchar('link');
                $schema->longtext('description');
                $schema->bool('state');
                $schema->integer('ordering')->comment('Ordering');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('category_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Banner::class);
    }
};
