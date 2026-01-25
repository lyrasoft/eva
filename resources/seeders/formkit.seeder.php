<?php

declare(strict_types=1);

namespace Lyrasoft\Formkit\Seeder;

use Lyrasoft\Formkit\Entity\Formkit;
use Lyrasoft\Formkit\Entity\FormkitResponse;
use Lyrasoft\Formkit\Enum\ResState;
use Lyrasoft\Formkit\FormkitPackage;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Http\Browser;
use Windwalker\Core\Http\BrowserNext;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

return new /** Formkit Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(FormkitPackage $formkitPackage): void
    {
        $faker = $this->faker('en_US');

        /** @var EntityMapper<Formkit> $mapper */
        $mapper = $this->orm->mapper(Formkit::class);
        $userIds = $this->orm->findColumn(User::class, 'id')->map('intval')->dump();

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

            $this->printCounting();

            foreach (range(1, 30) as $k) {
                $agent = new BrowserNext([], $faker->userAgent());

                $res = new FormkitResponse();
                $res->formkitId = $item->id;
                $res->content = $resContent;
                $res->state = $faker->randomElement(ResState::cases());
                $res->from = $faker->url();
                $res->ip = $faker->ipv4();
                $res->ua = $agent->userAgent;
                $res->browser = $agent->browserString() . '/' . $agent->engine->toString();
                $res->device = $agent->device->type . '/' . ($agent->deviceString() ?: 'PC');
                $res->os = $agent->osString();

                $this->orm->createOne($res);

                $this->printCounting();
            }
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Formkit::class, FormkitResponse::class);
    }
};
