<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Attributes\Enum\Title;
use Windwalker\Utilities\Enum\EnumRichInterface;
use Windwalker\Utilities\Enum\EnumRichTrait;

enum BannerType: string implements EnumRichInterface
{
    use EnumRichTrait;

    #[Title('首頁橫幅')]
    case HOME = 'home';
}
