<?php

declare(strict_types=1);

namespace App\Traits;

use App\Service\PriceFormatter;
use Windwalker\DI\Attributes\Inject;

trait PriceFormatTrait
{
    #[Inject]
    protected PriceFormatter $priceFormatter;

    public function priceFormat(mixed $price): string
    {
        return $this->priceFormatter->format($price);
    }
}
