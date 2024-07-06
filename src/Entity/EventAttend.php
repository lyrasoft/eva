<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use App\Enum\AttendState;
use DateTimeInterface;
use Lyrasoft\Luna\Attributes\Author;
use Lyrasoft\Luna\Attributes\Modifier;
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
use Windwalker\ORM\Metadata\EntityMetadata;

#[Table('event_attends', 'event_attend')]
#[AllowDynamicProperties]
class EventAttend implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('order_id')]
    protected int $orderId = 0;

    #[Column('user_id')]
    protected int $userId = 0;

    #[Column('event_id')]
    protected int $eventId = 0;

    #[Column('stage_id')]
    protected int $stageId = 0;

    #[Column('plan_id')]
    protected int $planId = 0;

    #[Column('plan_title')]
    protected string $planTitle = '';

    #[Column('price')]
    protected float $price = 0.0;

    #[Column('name')]
    protected string $name = '';

    #[Column('email')]
    protected string $email = '';

    #[Column('nick')]
    protected string $nick = '';

    #[Column('mobile')]
    protected string $mobile = '';

    #[Column('phone')]
    protected string $phone = '';

    #[Column('address')]
    protected string $address = '';

    #[Column('details')]
    #[Cast(JsonCast::class)]
    protected array $details = [];

    #[Column('screenshots')]
    #[Cast(JsonCast::class)]
    protected array $screenshots = [];

    #[Column('state')]
    #[Cast(AttendState::class)]
    protected AttendState $state;

    #[Column('checked_in_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $checkedInAt = null;

    #[Column('alternate')]
    #[Cast('bool', 'int')]
    protected bool $alternate = false;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

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

    public function getStageId(): int
    {
        return $this->stageId;
    }

    public function setStageId(int $stageId): static
    {
        $this->stageId = $stageId;

        return $this;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function setPlanId(int $planId): static
    {
        $this->planId = $planId;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getScreenshots(): array
    {
        return $this->screenshots;
    }

    public function setScreenshots(array $screenshots): static
    {
        $this->screenshots = $screenshots;

        return $this;
    }

    public function getState(): AttendState
    {
        return $this->state;
    }

    public function setState(string|AttendState $state): static
    {
        $this->state = AttendState::wrap($state);

        return $this;
    }

    public function getCheckedInAt(): ?Chronos
    {
        return $this->checkedInAt;
    }

    public function setCheckedInAt(DateTimeInterface|string|null $checkedInAt): static
    {
        $this->checkedInAt = Chronos::tryWrap($checkedInAt);

        return $this;
    }

    public function isAlternate(): bool
    {
        return $this->alternate;
    }

    public function setAlternate(bool $alternate): static
    {
        $this->alternate = $alternate;

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

    public function getPlanTitle(): string
    {
        return $this->planTitle;
    }

    public function setPlanTitle(string $planTitle): static
    {
        $this->planTitle = $planTitle;

        return $this;
    }
}
