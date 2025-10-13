<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Sequence\Entity\Sequence;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022030307270001_SequenceInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Sequence::class,
            function (Schema $schema) {
                $schema->varchar('type');
                $schema->varchar('prefix');
                $schema->integer('serial');

                $schema->addPrimaryKey(['type', 'prefix']);
                $schema->addIndex('type');
                $schema->addIndex('prefix');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Sequence::class);
    }
};
