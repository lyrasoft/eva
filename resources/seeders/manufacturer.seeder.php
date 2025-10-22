<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\Services\LocaleService;
use Lyrasoft\ShopGo\Entity\Manufacturer;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

return new /** Address Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<Manufacturer> $mapper */
        $mapper = $this->orm->mapper(Manufacturer::class);

        /** @var EntityMapper<TagMap> $tagMapMapper */
        $tagMapMapper = $this->orm->mapper(TagMap::class);

        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        $tagIds = $this->orm->findColumn(Tag::class, 'id')->dump();
        $langCodes = LocaleService::getSeederLangCodes($this->orm);

        foreach (range(1, 30) as $i) {
            $langCode = $faker->randomElement($langCodes);
            $item = $mapper->createEntity();

            $faker = $this->faker($langCode);

            $item->title = Utf8String::ucwords(
                $faker->company()
            );
            $item->alias = SlugHelper::safe($item->title);
            $item->image = $faker->unsplashImage();
            $item->introtext = $faker->paragraph(5);
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            $item->ordering = $i;
            $item->meta = [
                'title' => $item->title,
                'description' => $faker->paragraph(),
                'keywords' => implode(',', $faker->words()),
            ];
            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+10days');
            $item->createdBy = (int) $faker->randomElement($userIds);
            $item->language = $langCode;
            $item->params = [];

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, 3) as $tagId) {
                $tagMapItem = $tagMapMapper->createEntity();

                $tagMapItem->type = 'manufacturer';
                $tagMapItem->targetId = $item->id;
                $tagMapItem->tagId = (int) $tagId;

                $tagMapMapper->createOne($tagMapItem);
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Manufacturer::class);
    }
};
