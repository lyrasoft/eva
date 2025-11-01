<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Favorite\Entity\Favorite;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Database\Schema\Schema;

return new /** 2022102708280008_FavoriteInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Favorite::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->char('type')->length(20);
                $schema->varchar('user_id')->length(120);
                $schema->varchar('target_id')->length(120);
                $schema->datetime('created');

                $schema->addIndex('type');
                $schema->addIndex('user_id');
                $schema->addIndex('target_id');
                $schema->addUniqueKey(['type', 'user_id', 'target_id']);
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Favorite::class);
    }
};
