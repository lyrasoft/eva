<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Enum\BannerVideoType;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;

use function Windwalker\value;

return new /** Banner Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(BannerService $bannerService): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<Banner> $mapper */
        $mapper = $this->orm->mapper(Banner::class);
        // $langCodes = LocaleService::getSeederLangCodes($this->orm);
        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'banner'])->dump();
        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        /** @var EnumTranslatableInterface|\UnitEnum $typeEnum */
        $typeEnum = $bannerService->getTypeEnum();

        $mediaTypes = ['image', 'video'];

        foreach (range(1, 15) as $i) {
            $item = $mapper->createEntity();

            $item->title = $faker->sentence(2);
            $item->subtitle = $faker->sentence(4);
            $item->description = $faker->sentence(7);

            if ($typeEnum) {
                $item->type = value($faker->randomElement($typeEnum::cases()));
            } else {
                $item->categoryId = (int) $faker->randomElement($categoryIds);
            }

            $mediaType = $faker->randomElement($mediaTypes);

            if ($mediaType === 'video') {
                /** @var BannerVideoType $videoType */
                $videoType = $faker->randomElement(BannerVideoType::cases());
                $item->videoType = $videoType;

                if ($videoType === BannerVideoType::EMBED) {
                    $item->video = 'https://www.youtube.com/watch?v=jfKfPfyJRdk';
                    $item->mobileVideo = 'https://www.youtube.com/watch?v=rUxyKA_-grg';
                } else {
                    $item->video = 'https://lyratest.ap-south-1.linodeobjects.com/video/landscape-1080.mp4';
                    $item->mobileVideo = 'https://lyratest.ap-south-1.linodeobjects.com/video/mobile-720.mp4';
                }
            }

            $item->image = $faker->unsplashImage(1920, 800);
            $item->mobileImage = $faker->unsplashImage(720, 720);
            $item->link = 'https://simular.co';

            $item->language = '*';
            $item->state = 1;
            $item->ordering = $i;
            $item->created = $faker->dateTimeThisYear();
            $item->modified = $item->created->modify('+10days');
            $item->createdBy = (int) $faker->randomElement($userIds);

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Banner::class);
    }
};
