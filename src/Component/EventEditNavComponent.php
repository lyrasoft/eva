<?php

declare(strict_types=1);

namespace App\Component;

use Closure;
use Windwalker\Core\Edge\Attribute\EdgeComponent;
use Windwalker\Edge\Component\AbstractComponent;
use Windwalker\Utilities\Attributes\Prop;

#[EdgeComponent('event-edit-nav')]
class EventEditNavComponent extends AbstractComponent
{
    #[Prop]
    public ?int $eventId = null;

    public function render(): Closure|string
    {
        return 'components.event-edit-nav';
    }
}
