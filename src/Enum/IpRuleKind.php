<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Attributes\Enum\Color;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum IpRuleKind: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    #[Color('danger')]
    case BLOCK_LIST = 'block';

    #[Color('success')]
    case ALLOW_LIST = 'allow';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('firewall.ip.rule.kind.' . $this->getKey());
    }
}
