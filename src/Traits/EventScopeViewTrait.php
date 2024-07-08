<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\Event;
use App\Entity\EventStage;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\View\View;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

trait EventScopeViewTrait
{
    use InstanceCacheTrait;

    public function getCurrentEvent(AppContext $app): Event
    {
        return $this->once(
            'current.event',
            fn() => $this->orm->mustFindOne(Event::class, $app->input('eventId'))
        );
    }

    public function prepareCurrentEvent(AppContext $app, View $view): Event
    {
        $event = $this->getCurrentEvent($app);

        $view[$event::class] = $event;

        return $event;
    }

    public function getCurrentEventStage(AppContext $app): EventStage
    {
        return $this->once(
            'current.event.stage',
            fn () => $this->orm->mustFindOne(EventStage::class, $app->input('eventStageId'))
        );
    }

    /**
     * @param  AppContext  $app
     *
     * @return  array{ 0: Event, 1: EventStage }
     */
    public function getCurrentEventAndStage(AppContext $app): array
    {
        return [
            $this->getCurrentEvent($app),
            $this->getCurrentEventStage($app),
        ];
    }

    /**
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  array{ 0: Event, 1: EventStage }
     */
    public function prepareCurrentEventAndStage(AppContext $app, View $view): array
    {
        [$event, $stage] = $this->getCurrentEventAndStage($app);

        $view[$event::class] = $event;
        $view[$stage::class] = $stage;

        return [$event, $stage];
    }
}
