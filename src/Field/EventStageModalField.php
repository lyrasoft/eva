<?php

declare(strict_types=1);

namespace App\Field;

use App\Entity\EventStage;
use Psr\Http\Message\UriInterface;
use Unicorn\Field\ModalField;
use Windwalker\Uri\Uri;

class EventStageModalField extends ModalField
{
    protected ?int $eventId = null;

    protected function configure(): void
    {
        $this->route('event_stage_list');
        $this->table(EventStage::class);
    }

    protected function getDefaultUrl(): UriInterface|string|null
    {
        $url = parent::getDefaultUrl();

        if (!$url) {
            return null;
        }

        if ($this->getEventId()) {
            $url = $url->withVar('eventId', $this->getEventId());
        }

        return $url;
    }

    /**
     * @return  array
     */
    protected function getAccessors(): array
    {
        return array_merge(
            parent::getAccessors(),
            []
        );
    }

    public function getEventId(): ?int
    {
        return $this->eventId;
    }

    public function setEventId(?int $eventId): static
    {
        $this->eventId = $eventId;

        return $this;
    }
}
