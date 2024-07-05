<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

#[Table('event_member_maps', 'event_member_map')]
#[AllowDynamicProperties]
class EventMemberMap implements EntityInterface
{
    use EntityTrait;

    #[Column('type')]
    protected string $type = '';

    #[Column('member_id')]
    protected int $memberId = 0;

    #[Column('target_id')]
    protected int $targetId = 0;

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function setMemberId(int $memberId): static
    {
        $this->memberId = $memberId;

        return $this;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;

        return $this;
    }
}
