<?php

declare(strict_types=1);

namespace App\Service;

use Lyrasoft\Sequence\Service\SequenceService;
use Windwalker\DI\Attributes\Service;

#[Service]
class EventOrderService
{
    public function __construct(protected SequenceService $sequenceService)
    {
    }

    public function createNo()
    {
        //
    }
}
