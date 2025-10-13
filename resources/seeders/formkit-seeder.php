<?php

declare(strict_types=1);

namespace Lyrasoft\Formkit\Seeder;

use Lyrasoft\Formkit\Entity\Formkit;
use Lyrasoft\Formkit\Entity\FormkitResponse;
use Lyrasoft\Formkit\Enum\ResState;
use Lyrasoft\Formkit\FormkitPackage;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Http\Browser;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Formkit Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (FormkitPackage $formkitPackage) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Formkit> $mapper */
        $mapper = $orm->mapper(Formkit::class);
        $userIds = $orm->findColumn(User::class, 'id')->map('intval')->dump();

        $content = json_decode(file_get_contents(__DIR__ . '/data/formkit.json'), true);
        $resContent = json_decode(file_get_contents(__DIR__ . '/data/formkit_response.json'), true);

        foreach (range(1, 30) as $i) {
            $published = $faker->dateTimeThisYear();

            $item = $mapper->createEntity();

            $item->title = $faker->sentence(2);

            $item->alias = $item->title;
            $item->image = $faker->unsplashImage(1600, 900);
            $item->content = $content;
            $item->state = 1;
            $item->extends = $formkitPackage->getDefaultExtends();
            $item->publishUp = random_int(0, 3) ? null : $published;
            $item->publishDown = random_int(0, 3) ? null : $published->modify('+60days');

            $item = $mapper->createOne($item);

            $seeder->outCounting();

            foreach (range(1, 30) as $k) {
                $agent = new Browser([], $faker->userAgent());

                $res = new FormkitResponse();
                $res->formkitId = $item->id;
                $res->content = $resContent;
                $res->state = $faker->randomElement(ResState::cases());
                $res->from = $faker->url();
                $res->ip = $faker->ipv4();
                $res->ua = $agent->getUserAgent();
                $res->browser = $agent->browser() . '/' . $agent->version($agent->browser());
                $res->device = $agent->deviceType() . '/' . ($agent->device() ?: 'PC');
                $res->os = $agent->platform();

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
