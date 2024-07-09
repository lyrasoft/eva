<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum EventOrderState: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case UNPAID = 'unpaid';
    case PENDING_APPROVAL = 'pending_approval';
    // case PAID = 'paid';
    case DONE = 'done';
    case CANCEL = 'cancel';
    case FAIL = 'fail';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('event.order.state.' . $this->getKey());
    }
}
