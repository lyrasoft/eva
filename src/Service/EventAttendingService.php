<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\EventAttendingPlan;
use App\Data\EventAttendingStore;
use App\Data\EventOrderTotal;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;
use Windwalker\Query\Query;

/**
 * @psalm-type AttendingData = array{
 *      order: array<string, mixed>,
 *      quantity: array<int, int>,
 *      attends: array<string, array<string, mixed>>
 * }
 */
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
     * @return  null|AttendingData
     */
    public function getAttendingDataFromSession(int $stageId): ?array
    {
        return $this->app->state->get(static::getAttendingSessionKey($stageId));
    }

    /**
     * @param  int    $stageId
     * @param  array  $data
     *
     * @return  AttendingData|null
     */
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

    public function validatePlan(EventStage $stage, int $planId, int $qty, bool $lock = false): EventPlan
    {
        if ($lock) {
            $plan = $this->orm->mustFindOne(EventPlan::class, $planId);
        } else {
            $plan = $this->orm->from(EventPlan::class)
                ->where('id', $planId)
                ->forUpdate()
                ->get(EventPlan::class);
        }

        /** @var EventPlan $plan */

        if (
            $plan->getStageId() !== $stage->getId()
            || !$plan->isPublishUp()
        ) {
            throw new ValidateFailException('Plan is invalid');
        }

        // Todo: handle Alternates
        if ($plan->getQuota() < ($plan->getSold() + $qty)) {
            throw new ValidateFailException('方案：' . $plan->getTitle() . ' 名額已滿');
        }

        return $plan;
    }

    public function getAttendingStore(EventStage|int $stage, bool $lock = false): EventAttendingStore
    {
        if (is_int($stage)) {
            /** @var EventStage $stage */
            $stage = $this->orm->from(EventStage::class)
                ->where('id', $stage)
                ->tapIf(
                    $lock,
                    fn (Query $query) => $query->forUpdate()
                )
                ->get(EventStage::class);
        }

        $data = $this->getAttendingDataFromSession($stage->getId());

        $store = new EventAttendingStore();

        $store->setStage($stage);
        $store->setOrderData($data['order'] ?? []);

        $plans = &$store->getAttendingPlans();
        $attendGroup = $data['attends'] ?? [];

        foreach ($data['quantity'] ?? [] as $planId => $qty) {
            if (!$qty) {
                continue;
            }

            try {
                $plan = $this->validatePlan($stage, $planId, (int) $qty, $lock);
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
            $planData->setAttends(array_values($attends));

            $plans[] = $planData;
        }

        // Check stage quota
        // Todo: Handle Alternate
        if ($stage->getQuota() < ($stage->getAttends() + $store->getTotalQuantity())) {
            throw new ValidateFailException('活動名額已滿');
        }

        $totals = $store->getTotals();
        $totals->set(
            'grand_total',
            (new EventOrderTotal())
                ->setTitle('總計')
                ->setValue($store->getGrandTotal()->toFloat())
        );

        return $store;
    }

    public function getAttendingStorageThenForget(EventStage $stage): EventAttendingStore
    {
        $data = $this->getAttendingStore($stage);

        $this->forgetAttendingData($stage->getId());

        return $data;
    }
}
