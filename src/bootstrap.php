<?php

declare(strict_types=1);

//

namespace EventBooking {
    function priceFormat(string|float|int|null $num, string $prefix = ''): string
    {
        if (!is_numeric($num)) {
            return '';
        }

        $n = (float) $num;

        $negative = $n < 0;

        $price = $prefix . number_format(abs($n));

        return $negative ? '-' . $price : $price;
    }
}
