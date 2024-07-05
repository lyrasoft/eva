<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum AttendState: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case PENDING = 'pending';
    case CHECKED_IN = 'checked_in';
    case CANCEL = 'cancel';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('event.attend.state.' . $this->getKey());
    }
}
