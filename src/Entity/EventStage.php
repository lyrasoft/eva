<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use DateTimeInterface;
use Lyrasoft\Luna\Attributes\Author;
use Lyrasoft\Luna\Attributes\Modifier;
use Lyrasoft\Luna\Attributes\Slugify;
use Unicorn\Attributes\NewOrdering;
use Unicorn\Enum\BasicState;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\DateTime\ServerTimeCast;
use Windwalker\ORM\Attributes\AutoIncrement;
use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\CreatedTime;
use Windwalker\ORM\Attributes\CurrentTime;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\Cast\JsonCast;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Event\AfterCopyEvent;
use Windwalker\ORM\Event\BeforeCopyEvent;
use Windwalker\ORM\Metadata\EntityMetadata;
use Windwalker\Utilities\Str;

#[Table('event_stages', 'event_stage')]
#[AllowDynamicProperties]
class EventStage implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('event_id')]
    protected int $eventId = 0;

    #[Column('venue_id')]
    protected int $venueId = 0;

    #[Column('title')]
    protected string $title = '';

    #[Column('alias')]
    #[Slugify]
    protected string $alias = '';

    #[Column('description')]
    protected string $description = '';

    #[Column('attend_url')]
    protected string $attendUrl = '';

    #[Column('quota')]
    protected ?int $quota = null;

    #[Column('alternate')]
    protected ?int $alternate = null;

    #[Column('less')]
    protected ?int $less = null;

    #[Column('attends')]
    protected int $attends = 0;

    #[Column('state')]
    #[Cast('int')]
    #[Cast(BasicState::class)]
    protected BasicState $state;

    #[Column('ordering')]
    protected int $ordering = 0;

    #[Column('start_date')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $startDate = null;

    #[Column('end_date')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $endDate = null;

    #[Column('created')]
    #[CastNullable(ServerTimeCast::class)]
    #[CreatedTime]
    protected ?Chronos $created = null;

    #[Column('modified')]
    #[CastNullable(ServerTimeCast::class)]
    #[CurrentTime]
    protected ?Chronos $modified = null;

    #[Column('created_by')]
    #[Author]
    protected int $createdBy = 0;

    #[Column('modified_by')]
    #[Modifier]
    protected int $modifiedBy = 0;

    #[Column('params')]
    #[Cast(JsonCast::class)]
    protected array $params = [];

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    #[BeforeCopyEvent]
    public static function beforeCopy(BeforeCopyEvent $event): void
    {
        $data = &$event->getData();

        $mapper = $event->getEntityMapper();

        while ($mapper->findOne(['title' => $data['title'], 'event_id' => $data['event_id']])) {
            $data['title'] = Str::increment($data['title'], '%s %d');
        }

        while ($mapper->findOne(['alias' => $data['alias'], 'event_id' => $data['event_id']])) {
            $data['alias'] = Str::increment($data['alias'], '%s-%d');
        }

        $data['state'] = 0;
        $data['attends'] = 0;
    }

    #[AfterCopyEvent]
    public static function afterCopy(AfterCopyEvent $event): void
    {
        $old = $event->getOldEntity();

        /** @var static $item */
        $item = $event->getEntity();
        $orm = $event->getORM();

        $orm->copy(
            EventPlan::class,
            [
                'stage_id' => $old?->getId()
            ],
            [
                'stage_id' => $item->getId()
            ]
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): static
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getVenueId(): int
    {
        return $this->venueId;
    }

    public function setVenueId(int $venueId): static
    {
        $this->venueId = $venueId;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAttendUrl(): string
    {
        return $this->attendUrl;
    }

    public function setAttendUrl(string $attendUrl): static
    {
        $this->attendUrl = $attendUrl;

        return $this;
    }

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(?int $quota): static
    {
        $this->quota = $quota;

        return $this;
    }

    public function getAlternate(): ?int
    {
        return $this->alternate;
    }

    public function setAlternate(?int $alternate): static
    {
        $this->alternate = $alternate;

        return $this;
    }

    public function getLess(): ?int
    {
        return $this->less;
    }

    public function setLess(?int $less): static
    {
        $this->less = $less;

        return $this;
    }

    public function getState(): BasicState
    {
        return $this->state;
    }

    public function setState(int|BasicState $state): static
    {
        $this->state = BasicState::wrap($state);

        return $this;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }

    public function setOrdering(int $ordering): static
    {
        $this->ordering = $ordering;

        return $this;
    }

    public function getStartDate(): ?Chronos
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface|string|null $startDate): static
    {
        $this->startDate = Chronos::tryWrap($startDate);

        return $this;
    }

    public function getEndDate(): ?Chronos
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeInterface|string|null $endDate): static
    {
        $this->endDate = Chronos::tryWrap($endDate);

        return $this;
    }

    public function getCreated(): ?Chronos
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface|string|null $created): static
    {
        $this->created = Chronos::tryWrap($created);

        return $this;
    }

    public function getModified(): ?Chronos
    {
        return $this->modified;
    }

    public function setModified(DateTimeInterface|string|null $modified): static
    {
        $this->modified = Chronos::tryWrap($modified);

        return $this;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedBy(): int
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(int $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function getAttends(): int
    {
        return $this->attends;
    }

    public function setAttends(int $attends): static
    {
        $this->attends = $attends;

        return $this;
    }
}
