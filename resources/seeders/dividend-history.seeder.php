<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Investor\Entity\DividendHistory;
use Lyrasoft\Luna\Services\AssociationService;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;

use function Windwalker\chronos;

return new /** DividendHistory Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(AssociationService $associationService, LocaleService $localeService): void
    {
        $faker = $this->faker('zh_TW');

        $langCodes = LocaleService::getSeederLangCodes($this->orm);
        $mapper = $this->orm->mapper(DividendHistory::class);
        $thisYear = chronos('now')->year;
        $years = range(2018, $thisYear);

        foreach ($years as $year) {
            if ($localeService->isEnabled()) {
                $chooseLangCodes = $langCodes;
            } else {
                $chooseLangCodes = [$faker->randomElement($langCodes)];
            }

            $assocIds = [];

            foreach ($chooseLangCodes as $chooseLangCode) {
                if ($chooseLangCode === '*') {
                    continue;
                }

                $faker = $this->faker($chooseLangCode);

                $item = $mapper->createEntity();

                $item->title = (string) $year;
                $item->stockDividend = $faker->randomFloat(6, 0, 10);
                $item->cashDividend = $faker->randomFloat(6, 0, 10);
                $item->exDividendDate = chronos("{$year}-" . $faker->date('m-d'));
                $item->recordDate = chronos("{$year}-" . $faker->date('m-d'));
                $item->paymentDate = chronos("{$year}-" . $faker->date('m-d'));
                $item->state = BasicState::PUBLISHED;
                $item->language = $chooseLangCode;

                $this->orm->createOne($item);

                $assocIds[$chooseLangCode] = $item->id;

                $this->printCounting();
            }

            $associationService->createAssociations(
                'dividend_history',
                $assocIds,
            );
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(DividendHistory::class);
    }
};
