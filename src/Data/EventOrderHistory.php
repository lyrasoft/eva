<?php

declare(strict_types=1);

namespace App\Data;

use App\Enum\EventOrderState;
use App\Enum\OrderHistoryType;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Data\ValueObject;

use function Windwalker\try_chronos;
use function Windwalker\uid;

class EventOrderHistory extends ValueObject
{
    public string $id = '';

    public OrderHistoryType $type;

    public EventOrderState $state;

    public string $stateText = '';

    public bool $notify = false;

    public string $message = '';

    public Chronos $created;

    public int $userId = 0;

    public string $userName = '';

    public function __construct(mixed $data = null)
    {
        $this->id = uid();
        $this->setCreated('now');

        parent::__construct($data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): OrderHistoryType
    {
        return $this->type;
    }

    public function setType(OrderHistoryType|string $type): static
    {
        $this->type = OrderHistoryType::wrap($type);

        return $this;
    }

    public function getState(): EventOrderState
    {
        return $this->state;
    }

    public function setState(EventOrderState|string $state): static
    {
        $this->state = EventOrderState::wrap($state);

        return $this;
    }

    public function getStateText(): string
    {
        return $this->stateText;
    }

    public function setStateText(string $stateText): static
    {
        $this->stateText = $stateText;

        return $this;
    }

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): static
    {
        $this->notify = $notify;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreated(): Chronos
    {
        return $this->created;
    }

    public function setCreated(string|\DateTimeInterface $created): static
    {
        $this->created = try_chronos($created);

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

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }
}
