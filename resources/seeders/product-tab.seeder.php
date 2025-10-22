<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Page;
use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

return new /** ProductTab Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductTab> $mapper */
        $mapper = $this->orm->mapper(ProductTab::class);
        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();
        $articleIds = $this->orm->findColumn(Article::class, 'id')->dump();
        $pageIds = $this->orm->findColumn(Page::class, 'id')->dump();

        foreach (range(1, 20) as $i) {
            $item = $mapper->createEntity();

            $contentType = $faker->randomElement(['article', 'page', 'content']);

            $item->title = $faker->sentence(1);

            if ($contentType === 'article') {
                $item->articleId = (int) $faker->randomElement($articleIds);
            } elseif ($contentType === 'page') {
                $item->pageId = (int) $faker->randomElement($pageIds);
            } else {
                $item->content = $faker->paragraph(20);
            }

            $item->ordering = $i;
            $item->state = 1;

            $tab = $mapper->createOne($item);

            $categoryCount = random_int(0, 3);

            if ($categoryCount > 0) {
                $cids = $faker->randomElements($categoryIds, $categoryCount);
                shuffle($cids);
                $cids = array_values($cids);

                foreach ($cids as $c => $cid) {
                    $map = new ShopCategoryMap();
                    $map->type = 'tab';
                    $map->categoryId = (int) $cid;
                    $map->targetId = $tab->id;
                    $map->ordering = $c + 1;

                    $this->orm->createOne(ShopCategoryMap::class, $map);
                }
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(ProductTab::class, ShopCategoryMap::class);
    }
};
