<?php

declare(strict_types=1);

namespace Lyrasoft\Portfolio\Seeder;

use Lyrasoft\Portfolio\Entity\Portfolio;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

use function Windwalker\collect;

return new /** Portfolio Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<Portfolio> $mapper */
        $mapper = $this->orm->mapper(Portfolio::class);
        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'portfolio'])->dump();
        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        $tagIds = $this->orm->findColumn(Tag::class, 'id')->dump();

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->title = $faker->sentence(3);
            $item->subtitle = $faker->sentence(2);
            $item->categoryId = (int) $faker->randomElement($categoryIds);
            $item->description = $faker->paragraph(10);
            $item->cover = $faker->unsplashImage(1600, 800);
            $item->images = collect($faker->unsplashImages(random_int(5, 8), 1000, 1000))
                ->map(fn($url) => [
                    'title' => '',
                    'url' => $url,
                    'description' => '',
                ])
                ->dump();
            $item->url = $faker->url();
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            $item->ordering = $i;
            $item->createdBy = (int) $faker->randomElement($userIds);
            $item->modifiedBy = (int) $faker->randomElement($userIds);
            $item->created = $created = $faker->dateTimeThisYear();
            $item->modified = $created->modify('+10days');

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->tagId = (int) $tagId;
                $map->type = 'portfolio';
                $map->targetId = $item->id;

                $this->orm->createOne(TagMap::class, $map);
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Portfolio::class);
    }
};
