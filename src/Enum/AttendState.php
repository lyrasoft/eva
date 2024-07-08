<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Attributes\Enum\Color;
use Windwalker\Utilities\Attributes\Enum\Icon;
use Windwalker\Utilities\Attributes\Enum\Title;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum AttendState: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    #[Title('待處理')]
    #[Color('warning')]
    #[Icon('far fa-clock')]
    case PENDING = 'pending';

    #[Title('可出席')]
    #[Color('primary')]
    #[Icon('far fa-calendar')]
    case BOOKED = 'booked';

    #[Title('已簽到')]
    #[Color('success')]
    #[Icon('far fa-check-circle')]
    case CHECKED_IN = 'checked_in';

    #[Title('已取消')]
    #[Color('secondary')]
    #[Icon('far fa-xmark')]
    case CANCEL = 'cancel';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('event.attend.state.' . $this->getKey());
    }
}
