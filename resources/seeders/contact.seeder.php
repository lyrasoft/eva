<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Contact\Entity\Contact;
use Lyrasoft\Contact\Enum\ContactState;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Contact Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('en_US');

        /** @var EntityMapper<Contact> $mapper */
        $mapper = $this->orm->mapper(Contact::class);
        $userIds = $this->orm->findColumn(User::class, 'id')->dump();

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->title = $faker->sentence(2);
            $item->type = 'main';
            $item->name = $faker->name();
            $item->email = $faker->safeEmail();
            $item->phone = $faker->phoneNumber();
            $item->state = $faker->randomElement(ContactState::values());
            $item->url = $faker->url();
            $item->content = $faker->paragraph(10);
            $item->assigneeId = (int) $faker->randomElement($userIds);
            $item->created = $faker->dateTimeThisYear();
            $item->details = [
                'address' => $faker->streetAddress(),
                'zip' => $faker->postcode()
            ];
            $item->params = [
                'ip' => $faker->ipv4(),
            ];

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Contact::class);
    }
};
