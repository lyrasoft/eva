<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\ShopGo\Entity\Address;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\AbstractMigration;
use Windwalker\Core\Migration\MigrateDown;
use Windwalker\Core\Migration\MigrateUp;
use Windwalker\Database\Schema\Schema;

return new /** 2022122708280006_AddressInit */ class extends AbstractMigration {
    #[MigrateUp]
    public function up(): void
    {
        $this->createTable(
            Address::class,
            function (Schema $schema) {
                $schema->primary('id');
                $schema->integer('user_id');
                $schema->integer('location_id');
                $schema->varchar('firstname');
                $schema->varchar('lastname');
                $schema->varchar('name');
                $schema->varchar('email');
                $schema->varchar('phone');
                $schema->varchar('mobile');
                $schema->varchar('company');
                $schema->varchar('country');
                $schema->varchar('state');
                $schema->varchar('city');
                $schema->varchar('postcode');
                $schema->varchar('address1');
                $schema->varchar('address2');
                $schema->varchar('vat');
                $schema->varchar('formatted');
                $schema->json('details')->nullable(true);
                $schema->bool('enabled');
                $schema->datetime('created');
                $schema->datetime('modified');

                $schema->addIndex('user_id');
                $schema->addIndex('location_id');
            }
        );
    }

    #[MigrateDown]
    public function down(): void
    {
        $this->dropTables(Address::class);
    }
};
