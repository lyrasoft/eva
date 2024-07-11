<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Attributes\Enum\Color;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum EventOrderState: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    #[Color('warning')]
    case UNPAID = 'unpaid';

    #[Color('primary')]
    case PENDING_APPROVAL = 'pending_approval';
    // case PAID = 'paid';

    #[Color('success')]
    case DONE = 'done';

    #[Color('secondary')]
    case CANCEL = 'cancel';

    #[Color('danger')]
    case FAIL = 'fail';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('event.order.state.' . $this->getKey());
    }
}
