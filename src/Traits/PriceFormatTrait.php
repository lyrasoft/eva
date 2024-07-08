<?php

declare(strict_types=1);

namespace App\Traits;

use App\Service\PriceFormatService;
use Windwalker\DI\Attributes\Inject;

trait PriceFormatTrait
{
    #[Inject]
    protected PriceFormatService $priceFormatService;

    public function priceFormat(mixed $price): string
    {
        return $this->priceFormatService->format($price);
    }
}
