<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\ShopGoPackage;
use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Page;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * ProductTab Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (ShopGoPackage $shopGo) use ($seeder, $orm, $db) {
        $faker = $seeder->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductTab> $mapper */
        $mapper = $orm->mapper(ProductTab::class);
        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();
        $articleIds = $orm->findColumn(Article::class, 'id')->dump();
        $pageIds = $orm->findColumn(Page::class, 'id')->dump();

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

                    $orm->createOne(ShopCategoryMap::class, $map);
                }
            }

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(ProductTab::class, ShopCategoryMap::class);
    }
);
