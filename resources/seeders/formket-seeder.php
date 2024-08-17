<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Formkit;
use App\Entity\FormkitResponse;
use App\Enum\ResState;
use Jenssegers\Agent\Agent;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Formket Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Formkit> $mapper */
        $mapper = $orm->mapper(Formkit::class);
        $userIds = $orm->findColumn(User::class, 'id')->map('intval')->dump();

        $content = file_get_contents(__DIR__ . '/data/formkit.json');
        $resContent = file_get_contents(__DIR__ . '/data/formkit_response.json');

        foreach (range(1, 30) as $i) {
            $published = $faker->dateTimeThisYear();

            $item = $mapper->createEntity();

            $item->setTitle(
                $faker->sentence(2)
            );
            $item->setImage($faker->unsplashImage(1600, 900));
            $item->setContent($content);
            $item->setState(1);
            $item->setPublishUp(
                random_int(0, 3) ? null : $published
            );
            $item->setPublishDown(
                random_int(0, 3) ? null : $published->modify('+60days')
            );

            $item = $mapper->createOne($item);

            $seeder->outCounting();

            foreach (range(1, 30) as $k) {
                $agent = new Agent([], $faker->userAgent());

                $res = new FormkitResponse();
                $res->setFormketId($item->getId());
                $res->setContent($resContent);
                $res->setState(
                    $faker->randomElement(ResState::cases())
                );
                $res->setFrom($faker->url());
                $res->setIp($faker->ipv4());
                $res->setUa($agent->getUserAgent());
                $res->setBrowser($agent->browser() . '/' . $agent->version($agent->browser()));
                $res->setDevice($agent->deviceType() . '/' . ($agent->device() ?: 'PC'));
                $res->setOs($agent->platform());

                $orm->createOne($res);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Formkit::class, FormkitResponse::class);
    }
);
