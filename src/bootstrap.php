<?php

declare(strict_types=1);

//

namespace EventBooking {

    use Brick\Math\BigNumber;

    function priceFormat(mixed $num, string $prefix = ''): string
    {
        if ($num instanceof BigNumber) {
            $num = $num->toFloat();
        }

        if (!is_numeric($num)) {
            return '';
        }

        $n = (float) $num;

        $negative = $n < 0;

        $price = $prefix . number_format(abs($n));

        return $negative ? '-' . $price : $price;
    }
}
