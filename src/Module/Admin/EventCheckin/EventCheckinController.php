<?php

declare(strict_types=1);

namespace App\Module\Admin\EventCheckin;

use App\Entity\EventAttend;
use App\Entity\EventOrder;
use App\Enum\AttendState;
use App\Enum\EventOrderState;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\View\View;
use Windwalker\ORM\ORM;

#[Controller]
class EventCheckinController
{
    public function checkin(AppContext $app, ORM $orm)
    {
        $no = $app->input('attendNo');

        $attend = $orm->mustFindOne(EventAttend::class, compact('no'));
        $order = $orm->mustFindOne(EventOrder::class, $attend->getOrderId());

        /** @var View $view */
        $view = $app->make(EventCheckinView::class);

        if ($attend->getState() === AttendState::CHECKED_IN) {
            return $view->render();
        }

        if ($order->getState() !== EventOrderState::DONE) {
            return $view->render(['status' => 'fail', 'message' => '訂單未完成']);
        }

        $attend->setState(AttendState::CHECKED_IN);
        $attend->setCheckedInAt('now');
        $orm->updateOne($attend);

        return $view->render(['status' => 'fail', 'message' => '訂單未完成']);
    }
}
