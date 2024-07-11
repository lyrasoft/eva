<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\EventAttendingData;
use App\Data\EventAttendingPlan;
use App\Data\EventOrderTotal;
use App\Entity\EventAttend;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;

#[Service]
class EventAttendingService
{
    public function __construct(protected AppContext $app, protected ORM $orm)
    {
    }

    public static function getAttendingSessionKey(int $stageId): string
    {
        return "event.attending.$stageId";
    }

    /**
     * @param  int  $stageId
     *
     * @return  null|array{
     *     quantity: array<int, int>,
     *     attends: array
     * }
     */
    public function getAttendingDataFromSession(int $stageId): ?array
    {
        return $this->app->state->get(static::getAttendingSessionKey($stageId));
    }

    public function rememberAttendingData(int $stageId, array $data): ?array
    {
        return $this->app->state->remember(static::getAttendingSessionKey($stageId), $data);
    }

    public function getPlanAndQuantity(int $stageId, string $inputField = 'quantity'): array
    {
        $quantity = $this->app->input($inputField);

        if ($quantity !== null) {
            return $quantity;
        }

        $data = (array) $this->getAttendingDataFromSession($stageId);

        return $data['quantity'] ?? [];
    }

    public function forgetAttendingData(int $stageId): void
    {
        $this->app->state->forget(static::getAttendingSessionKey($stageId));
    }

    public function validatePlan(EventStage $stage, int $planId, int $qty): EventPlan
    {
        $plan = $this->orm->mustFindOne(EventPlan::class, $planId);

        if (
            $plan->getStageId() !== $stage->getId()
            || !$plan->isPublishUp()
        ) {
            throw new ValidateFailException('Plan is invalid');
        }

        return $plan;
    }

    public function getAttendingDataObject(EventStage $stage): EventAttendingData
    {
        $data = $this->getAttendingDataFromSession($stage->getId());

        $attendingData = new EventAttendingData();

        $plans = &$attendingData->getAttendingPlans();
        $attendGroup = $data['attends'] ?? [];

        foreach ($data['quantity'] as $planId => $qty) {
            if (!$qty) {
                continue;
            }

            try {
                $plan = $this->validatePlan($stage, $planId, (int) $qty);
            } catch (\Exception $e) {
                $this->forgetAttendingData($stage->getId());

                throw $e;
            }

            $attends = $attendGroup[$plan->getId()] ?? [];

            $planData = new EventAttendingPlan();
            $planData->setPlan($plan);
            $planData->setQuantity((int) $qty);
            $planData->setPrice($plan->getPrice());
            $planData->setTotal(
                $planData->getPrice()->multipliedBy((int) $qty)
            );
            $planData->setAttends($attends);

            $plans[] = $planData;
        }

        $totals = $attendingData->getTotals();
        $totals->set(
            'grand_total',
            (new EventOrderTotal())
                ->setTitle('總計')
                ->setValue($attendingData->getGrandTotal()->toFloat())
        );

        return $attendingData;
    }
}
