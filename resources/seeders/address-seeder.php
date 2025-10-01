<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Enum\LocationType;
use Lyrasoft\ShopGo\Service\LocationService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Address Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo, LocationService $locationService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Address> $mapper */
        $mapper = $orm->mapper(Address::class);

        $userIds = $orm->findColumn(User::class, 'id')->dump();

        $locations = $orm->findList(
            Location::class,
            [
                'type' => [LocationType::STATE(), LocationType::CITY()]
            ]
        )->all()->dump();

        foreach (range(1, 100) as $i) {
            $item = $mapper->createEntity();

            $location = $faker->randomElement($locations);
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->setUserId((int) $faker->randomElement($userIds));
            $item->setLocationId($location->getId());
            $item->setFirstname($faker->firstName());
            $item->setLastname($faker->lastName());
            $item->setName($item->getFirstname() . ' ' . $item->getLastname());
            $item->setEmail($faker->email());
            $item->setCountry($country->getTitle());
            $item->setState($state->getTitle());
            $item->setCompany($faker->company());
            $item->setAddress1($faker->address());
            $item->setAddress2('');
            $item->setCity('');
            $item->setPostcode((string) random_int(100, 9999));
            $item->setPhone($faker->phoneNumber());
            $item->setMobile('09' . random_int(10000000, 99999999));
            $item->setVat((string) random_int(10000000, 99999999));
            $item->setDetails([]);
            $item->setEnabled(true);
            $item->setFormatted(
                $locationService->formatAddress($item, true)
            );

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Address::class);
    }
);
