<?php

declare(strict_types=1);

namespace App\Service;

use Windwalker\DI\Attributes\Service;

#[Service]
class EventOrderStateService
{
    public static function colorToContrast(string $color, int $sep = 200): string
    {
        [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');

        $luma = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;

        return $luma > $sep ? 'black' : 'white';
    }

    public static function colorToCSS(string $color, int $sep = 200): string
    {
        $contrast = static::colorToContrast($color, $sep);

        return "background-color: $color; color: $contrast;";
    }
}
