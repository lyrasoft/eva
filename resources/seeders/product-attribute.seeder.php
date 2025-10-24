<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\ShopGo\Data\ListOption;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\Enum\ProductAttributeType;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\StrNormalize;

use function Windwalker\tid;

return new /** ProductAttribute Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductAttribute> $mapper */
        $mapper = $this->orm->mapper(ProductAttribute::class);
        $productCategoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'product'])->dump();
        $groupIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'attribute'])->dump();

        // Make Group Maps
        foreach ($groupIds as $groupId) {
            foreach ($faker->randomElements($productCategoryIds, 15) as $productCategoryId) {
                $map = new ShopCategoryMap();
                $map->type = 'attribute_group';
                $map->categoryId = (int) $productCategoryId;
                $map->targetId = (int) $groupId;

                $this->orm->createOne(ShopCategoryMap::class, $map);
            }
        }

        $groupIds[] = '0';

        foreach (range(1, 30) as $i) {
            $type = $faker->randomElement(ProductAttributeType::cases());
            $groupId = $faker->randomElement($groupIds);

            $item = $mapper->createEntity();
            $item->type = $type;
            $item->categoryId = (int) $groupId;
            $item->title = $faker->sentence(1);
            $item->key = StrNormalize::toSnakeCase(trim($item->title, '.'));
            $item->display = true;
            $item->ordering = $i;
            $item->state = 1;

            $options = [];

            if ($type === ProductAttributeType::SELECT) {
                foreach (range(1, random_int(5, 8)) as $o) {
                    $options[] = new ListOption(
                        value: strtolower($text = $faker->word()),
                        text: $text,
                        uid: tid(),
                    );
                }
            }

            $item->options = $options;

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(ProductAttribute::class);
    }
};
