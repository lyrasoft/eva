<?php

declare(strict_types=1);

namespace App\Data;

use Windwalker\Data\Collection;
use Windwalker\Utilities\TypeCast;

class EventOrderHistories extends Collection
{
    public function fill(mixed $data, array $options = []): static
    {
        $data = array_map(
            static fn ($item) => EventOrderHistory::wrap($item),
            TypeCast::toArray($data)
        );

        return parent::fill($data, $options);
    }
}