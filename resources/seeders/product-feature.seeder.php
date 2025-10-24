<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\ShopGo\Data\ListOption;
use Lyrasoft\ShopGo\Entity\ProductFeature;
use Lyrasoft\ShopGo\Enum\ProductFeatureType;
use Lyrasoft\ShopGo\ShopGoPackage;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;
use Windwalker\Utilities\Utf8String;

use function Windwalker\tid;

return new /** ProductFeature Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(ShopGoPackage $shopGo): void
    {
        $faker = $this->faker($shopGo->config('fixtures.locale') ?: 'en_US');

        /** @var EntityMapper<ProductFeature> $mapper */
        $mapper = $this->orm->mapper(ProductFeature::class);

        foreach (range(1, 10) as $i) {
            $type = $faker->randomElement(
                [
                    ProductFeatureType::SELECT,
                    ProductFeatureType::SELECT,
                    ProductFeatureType::COLOR,
                ]
            );

            $options = [];

            foreach (range(1, random_int(5, 8)) as $o) {
                $options[] = $option = new ListOption(
                    value: strtolower($text = Utf8String::ucwords($faker->word())),
                    text: $text,
                    uid: tid(),
                );

                if ($type === ProductFeatureType::COLOR) {
                    $option->color = $faker->safeHexColor();
                }
            }

            $item = $mapper->createEntity();
            $item->type = $type;
            $item->title = $faker->sentence(1);
            $item->default = $faker->randomElement($options)->value;
            $item->options = $options;
            $item->note = $faker->sentence();
            $item->ordering = $i;
            $item->state = 1;

            $mapper->createOne($item);

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(ProductFeature::class);
    }
};
