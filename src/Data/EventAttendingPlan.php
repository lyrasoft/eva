<?php

declare(strict_types=1);

namespace App\Data;

use App\Entity\EventPlan;
use Brick\Math\BigDecimal;
use Windwalker\Data\ValueObject;

class EventAttendingPlan extends ValueObject
{
    public EventPlan $plan;

    public int $quantity = 0;

    public BigDecimal $price;

    public BigDecimal $total;

    public array $attends = [];

    public array $attendEntities = [];

    public function getPlan(): EventPlan
    {
        return $this->plan;
    }

    public function setPlan(EventPlan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): BigDecimal
    {
        return $this->price;
    }

    public function setPrice(mixed $price): static
    {
        $this->price = BigDecimal::of($price);

        return $this;
    }

    public function getTotal(): BigDecimal
    {
        return $this->total;
    }

    public function setTotal(mixed $total): static
    {
        $this->total = BigDecimal::of($total);

        return $this;
    }

    public function &getAttends(): array
    {
        return $this->attends;
    }

    public function setAttends(array $attends): static
    {
        $this->attends = $attends;

        return $this;
    }

    public function &getAttendEntities(): array
    {
        return $this->attendEntities;
    }

    public function setAttendEntities(array $attendEntities): static
    {
        $this->attendEntities = $attendEntities;

        return $this;
    }
}
