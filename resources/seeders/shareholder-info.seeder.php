<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Investor\Data\Shareholder;
use Lyrasoft\Investor\Entity\ShareholderInfo;
use Lyrasoft\Luna\Services\AssociationService;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;

use function Windwalker\chronos;

return new /** ShareholderInfo Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(AssociationService $associationService, LocaleService $localeService): void
    {
        $faker = $this->faker('zh_TW');
        $samplePdf = 'https://www.adobe.com/support/products/enterprise/knowledgecenter/media/c4611_sample_explain.pdf';

        $langCodes = LocaleService::getSeederLangCodes($this->orm);
        $mapper = $this->orm->mapper(ShareholderInfo::class);
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
                $item->alias = (string) $year;
                $item->state = BasicState::PUBLISHED;
                $item->agmDate = chronos("{$year}-06-15");
                $item->agmLocation = $faker->address();
                $item->agmVideo = 'https://www.youtube.com/watch?v=2h8kiD1CScM';

                foreach (range(1, 10) as $s) {
                    $item->primaryShareholders->push(
                        new Shareholder(
                            name: $faker->boolean(50) ? $faker->name() : $faker->company(),
                            shares: (string) $faker->numberBetween(1000, 1000000),
                            percentage: (string) $faker->randomFloat(2, 0.1, 10.0),
                        )
                    );
                }

                $item->primaryShareholderDoc = $samplePdf;
                $item->showPrimary = $faker->boolean(80);

                foreach (range(1, 4) as $s) {
                    $item->topShareholders->push(
                        new Shareholder(
                            name: $faker->boolean(50) ? $faker->name() : $faker->company(),
                            shares: (string) $faker->numberBetween(1000, 1000000),
                            percentage: (string) $faker->randomFloat(2, 0.5, 10.0),
                        )
                    );
                }

                $item->noticeFile = $samplePdf;
                $item->agendaFile = $samplePdf;
                $item->minutesFile = $samplePdf;
                $item->state = BasicState::PUBLISHED;
                $item->language = $chooseLangCode;

                $this->orm->createOne($item);

                $assocIds[$chooseLangCode] = $item->id;

                $this->printCounting();
            }

            $associationService->createAssociations(
                'shareholder_info',
                $assocIds,
            );
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(ShareholderInfo::class);
    }
};
