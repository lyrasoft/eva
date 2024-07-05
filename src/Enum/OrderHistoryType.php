<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum OrderHistoryType: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case MEMBER = 'member';
    case ADMIN = 'admin';
    case SYSTEM = 'system';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('event.order.history.type.' . $this->getKey());
    }
}
