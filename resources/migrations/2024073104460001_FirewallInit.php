<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Firewall\Entity\IpRule;
use Lyrasoft\Firewall\Entity\Redirect;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2024073104460001_FirewallInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Redirect::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type');
                $schema->varchar('src')->length(1024);
                $schema->varchar('dest')->length(1024);
                $schema->integer('status');
                $schema->tinyint('state')->length(1);
                $schema->integer('ordering');
                $schema->text('note');
                $schema->integer('hits');
                $schema->datetime('last_hit');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('type');
                $schema->addIndex('src');
                $schema->addIndex('ordering');
            }
        );
        $this->createTable(
            IpRule::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->varchar('type');
                $schema->varchar('kind');
                $schema->varchar('range');
                $schema->text('paths');
                $schema->tinyint('state')->length(1);
                $schema->integer('ordering');
                $schema->text('note');
                $schema->datetime('expired_at');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->integer('created_by');
                $schema->integer('modified_by');
                $schema->json('params');

                $schema->addIndex('type');
                $schema->addIndex('ordering');
                $schema->addIndex('expired_at');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Redirect::class, IpRule::class);
    }
};
