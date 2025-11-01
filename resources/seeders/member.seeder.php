<?php

declare(strict_types=1);

namespace Lyrasoft\Member\Seeder;

use Lyrasoft\Member\Entity\Member;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;
use Windwalker\ORM\EntityMapper;

use function Windwalker\collect;

return new /** Member Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(): void
    {
        $faker = $this->faker('zh_TW');

        /** @var EntityMapper<Member> $mapper */
        $mapper = $this->orm->mapper(Member::class);
        $userIds = $this->orm->findColumn(User::class, 'id')->dump();
        $categoryIds = $this->orm->findColumn(Category::class, 'id', ['type' => 'member'])->dump();
        $tagIds = $this->orm->findColumn(Tag::class, 'id')->dump();

        foreach (range(1, 30) as $i) {
            $item = $mapper->createEntity();

            $item->name = $faker->name();
            $item->categoryId = (int) $faker->randomElement($categoryIds);
            $item->intro = $faker->paragraph(5);
            $item->description = $faker->paragraph(10);
            $item->image = $faker->avatar(400);
            $item->state = $faker->optional(0.7, 0)->passthrough(1);
            $item->ordering = $i;
            $item->createdBy = (int) $faker->randomElement($userIds);
            $item->modifiedBy = (int) $faker->randomElement($userIds);
            $item->created = $created = $faker->dateTimeThisYear();
            $item->modified = $created->modify('+10days');

            $item = $mapper->createOne($item);

            foreach ($faker->randomElements($tagIds, random_int(3, 5)) as $tagId) {
                $map = new TagMap();
                $map->targetId = $item->id;
                $map->tagId = (int) $tagId;
                $map->type = 'member';

                $this->orm->createOne(TagMap::class, $map);
            }

            $this->printCounting();
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(Member::class);
    }
};
