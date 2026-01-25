<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Faq\Entity\Faq;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** Faq Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('zh_TW');

        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'faq'])->dump();

        /** @var EntityMapper<Faq> $mapper */
        $mapper = $this->orm->mapper(Faq::class);

        foreach (range(1, 50) as $i) {
            /** @var Faq $item */
            $item = $mapper->createEntity();

            $item->categoryId = (int) $faker->randomElement($categoryIds);
            $item->title = $faker->sentence(2);
            $item->image = $faker->unsplashImage();
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            $item->description = $faker->paragraph(5);
            $item->ordering = $i;
            $item->created = $faker->dateTimeThisYear();
            $item->modified = (clone $item->created)->modify('+5 days');
            $item->createdBy = (int) $faker->randomElement($userIds);
            $item->modifiedBy = (int) $faker->randomElement($userIds);
            $item->params = [];

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Faq::class);
    }
};
