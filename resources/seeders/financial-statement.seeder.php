<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Investor\Data\MonthlyReport;
use Lyrasoft\Investor\Entity\FinancialStatement;
use Lyrasoft\Luna\Services\AssociationService;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Seed\AbstractSeeder;
use Windwalker\Core\Seed\SeedClear;
use Windwalker\Core\Seed\SeedImport;

use function Windwalker\chronos;
use function Windwalker\uid;

return new /** FinancialStatement Seeder */ class extends AbstractSeeder {
    #[SeedImport]
    public function import(AssociationService $associationService, LocaleService $localeService): void
    {
        $faker = $this->faker('zh_TW');
        $samplePdf = 'https://www.adobe.com/support/products/enterprise/knowledgecenter/media/c4611_sample_explain.pdf';

        $langCodes = LocaleService::getSeederLangCodes($this->orm);
        $mapper = $this->orm->mapper(FinancialStatement::class);
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

                $item = $mapper->createEntity();

                $item->title = (string) $year;
                $item->alias = (string) $year;
                $item->state = BasicState::PUBLISHED;
                $item->language = $chooseLangCode;
                $item->q1Consolidated = $samplePdf;
                $item->q2Consolidated = $samplePdf;
                $item->q3Consolidated = $samplePdf;
                $item->q4Consolidated = $samplePdf;
                $item->q4Individual = $samplePdf;
                $item->annualReport = $samplePdf;
                $item->earningsCallFiles = [
                    [
                        'uid' => uid(),
                        'url' => $samplePdf,
                    ],
                    [
                        'uid' => uid(),
                        'url' => $samplePdf,
                    ],
                    [
                        'uid' => uid(),
                        'url' => $samplePdf,
                    ],
                ];
                foreach (range(1, 12) as $m) {
                    $item->monthlyReport[$m] = new MonthlyReport(
                        revenue: (string) number_format((float) random_int(100000, 900000), 0),
                        yoy: (string) $faker->randomFloat(2, -40, 60) . '%',
                        cumulativeRevenue: (string) number_format((float) random_int(100000, 900000), 0),
                        cumulativeYoy: (string) $faker->randomFloat(2, -40, 60) . '%',
                        revenueSummary: $samplePdf
                    );
                }

                $this->orm->createOne($item);

                $assocIds[$chooseLangCode] = $item->id;

                $this->printCounting();
            }

            $associationService->createAssociations(
                'financial_statement',
                $assocIds,
            );
        }
    }

    #[SeedClear]
    public function clear(): void
    {
        $this->truncate(FinancialStatement::class);
    }
};
