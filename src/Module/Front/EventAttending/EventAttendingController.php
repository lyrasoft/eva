<?php

declare(strict_types=1);

namespace App\Module\Front\EventAttending;

use App\Entity\Event;
use App\Entity\EventStage;
use App\Service\EventAttendingService;
use App\Service\EventViewService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\ORM\ORM;

use function Windwalker\response;

#[Controller]
class EventAttendingController
{
    public function attending(
        AppContext $app,
        ORM $orm,
        EventAttendingService $eventAttendingService,
        EventViewService $eventViewService
    ): mixed {
        $stageId = (int) $app->input('stageId');

        if (!$stageId) {
            return response()->redirect($app->getSystemUri()->root());
        }

        $stage = $orm->mustFindOne(EventStage::class, $stageId);
        $event = $orm->mustFindOne(Event::class, $stage->getEventId());

        $eventViewService->checkEventAndStageAvailable($event, $stage);

        $quantity = $eventAttendingService->getPlanAndQuantity($stageId);

        $eventAttendingService->rememberAttendingData(
            $stageId,
            [
                'quantity' => $quantity,
                'attends' => []
            ]
        );

        $data = $eventAttendingService->getAttendingDataObject($stage);

        // Is empty
        if ($data->getTotalQuantity() === 0) {
            $app->addMessage('沒有報名資訊', 'warning');

            return $app->getNav()->back();
        }

        return $app->getNav()->to('event_attending')->var('stageId', $stage->getId());
    }

    public function checkout(AppContext $app)
    {
        show($app->input());
        
        exit(' @Checkpoint');
    }
}
