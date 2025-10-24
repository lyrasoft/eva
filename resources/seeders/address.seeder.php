<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Enum\LocationType;
use Lyrasoft\ShopGo\Service\LocationService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

return new /** Address Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo, LocationService $locationService): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Address> $mapper */
        $mapper = $this->orm->mapper(Address::class);

        $userIds = $this->orm->findColumn(User::class, 'id')->dump();

        $locations = $this->orm->findList(
            Location::class,
            [
                'type' => [LocationType::STATE, LocationType::CITY]
            ]
        )->all()->dump();

        foreach (range(1, 100) as $i) {
            $item = $mapper->createEntity();

            $location = $faker->randomElement($locations);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->userId = (int) $faker->randomElement($userIds);
            $item->locationId = $location->getId();
            $item->firstname = $faker->firstName();
            $item->lastname = $faker->lastName();
            $item->name = $item->firstname . ' ' . $item->lastname;
            $item->email = $faker->email();
            $item->country = $country->title;
            $item->state = $state->title;
            $item->company = $faker->company();
            $item->address1 = $faker->address();
            $item->address2 = '';
            $item->city = '';
            $item->postcode = (string) random_int(100, 9999);
            $item->phone = $faker->phoneNumber();
            $item->mobile = '09' . random_int(10000000, 99999999);
            $item->vat = (string) random_int(10000000, 99999999);
            $item->details = [];
            $item->enabled = true;
            $item->formatted = $locationService->formatAddress($item, true);

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Address::class);
    }
};
