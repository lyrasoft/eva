<?php

declare(strict_types=1);

namespace App\Data;

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Entity\EventStage;
use Brick\Math\BigDecimal;
use Windwalker\Data\Collection;
use Windwalker\Data\ValueObject;

use function Windwalker\collect;

class EventAttendingStore extends ValueObject
{
    public array $orderData = [];

    public ?EventOrder $order = null;

    public EventStage $stage;

    public EventOrderTotals $totals;

    public array $attendingPlans = [];

    public function getTotals(): EventOrderTotals
    {
        return $this->totals ??= new EventOrderTotals();
    }

    public function setTotals(EventOrderTotals|array $totals): static
    {
        $this->totals = EventOrderTotals::wrap($totals);

        return $this;
    }

    /**
     * @return  array<EventAttendingPlan>
     */
    public function &getAttendingPlans(): array
    {
        return $this->attendingPlans;
    }

    public function setAttendingPlans(array $attendingPlans): static
    {
        $this->attendingPlans = $attendingPlans;

        return $this;
    }

    /**
     * @return  Collection<array>
     */
    public function getAllAttends(): Collection
    {
        $attends = collect();

        foreach ($this->getAttendingPlans() as $attendingPlan) {
            $attends = $attends->append(...$attendingPlan->getAttends());
        }

        return $attends;
    }

    /**
     * @return  Collection<EventAttend>
     */
    public function getAllAttendEntities(): Collection
    {
        $attends = collect();

        foreach ($this->getAttendingPlans() as $attendingPlan) {
            $attends = $attends->append(...$attendingPlan->getAttendEntities());
        }

        return $attends;
    }

    public function getTotalQuantity(): int
    {
        $qty = 0;

        foreach ($this->getAttendingPlans() as $attendingPlan) {
            $qty += $attendingPlan->getQuantity();
        }

        return $qty;
    }

    public function getGrandTotal(): BigDecimal
    {
        $gt = BigDecimal::zero();

        foreach ($this->getAttendingPlans() as $attendingPlan) {
            $gt = $gt->plus($attendingPlan->getTotal());
        }

        return $gt;
    }

    public function getOrderData(): array
    {
        return $this->orderData;
    }

    public function setOrderData(array $orderData): static
    {
        $this->orderData = $orderData;

        return $this;
    }

    public function getOrder(): ?EventOrder
    {
        return $this->order;
    }

    public function setOrder(?EventOrder $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getStage(): EventStage
    {
        return $this->stage;
    }

    public function setStage(EventStage $stage): static
    {
        $this->stage = $stage;

        return $this;
    }
}
