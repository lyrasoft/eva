<?php

declare(strict_types=1);

namespace App\Module\Front\EventStage;

use App\Entity\Event;
use App\Entity\EventPlan;
use App\Entity\EventStage;
use App\Repository\EventStageRepository;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Module\Front\Category\CategoryViewTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Http\Browser;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

use function Windwalker\str;

#[ViewModel(
    layout: 'event-stage-item',
    js: 'event-stage-item.js'
)]
class EventStageItemView implements ViewModelInterface
{
    use CategoryViewTrait;

    public function __construct(
        protected ORM $orm,
        #[Autowire] protected EventStageRepository $repository
    ) {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app   The web app context.
     * @param  View        $view  The view object.
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): mixed
    {
        $id = $app->input('id');
        $alias = $app->input('alias');

        /** @var EventStage $item */
        $item = $this->repository->mustGetItem($id);

        if (!$item->getState()->isPublished()) {
            throw new RouteNotFoundException('Event Stage not found.');
        }

        if ($item->getPublishUp() && $item->getPublishUp()->isFuture()) {
            throw new RouteNotFoundException('Event Stage not started.');
        }

        if ($item->getEndDate() && $item->getEndDate()->isPast()) {
            throw new RouteNotFoundException('Event Stage was ended.');
        }

        $event = $this->orm->mustFindOne(Event::class, $item->getEventId());

        if (!$event->getState()->isPublished()) {
            throw new RouteNotFoundException('Event not found.');
        }

        if ($event->getPublishUp() && $event->getPublishUp()->isFuture()) {
            throw new RouteNotFoundException('Event not started.');
        }

        if ($event->getEndDate() && $event->getEndDate()->isPast()) {
            throw new RouteNotFoundException('Event was ended.');
        }

        /** @var Category $category */
        $category = $this->getCategoryOrFail($event->getCategoryId());

        if (!$category->getState()->isPublished()) {
            throw new RouteNotFoundException('Category not published.');
        }

        // Keep URL unique
        if (($item->getAlias() !== $alias) && !$app->retrieve(Browser::class)->isRobot()) {
            return $app->getNav()->self()->alias($item->getAlias());
        }

        $view[$item::class] = $item;
        $view[$event::class] = $event;

        // Plans
        $plans = $this->orm->from(EventPlan::class)
            ->where('state', 1)
            ->where('stage_id', $item->getId())
            ->order('start_date', 'ASC')
            ->all(EventPlan::class);

        return compact('item', 'event', 'category', 'plans');
    }

    #[ViewMetadata]
    public function prepareMetadata(HtmlFrame $htmlFrame, Event $event, EventStage $item): void
    {
        $htmlFrame->setTitle($event->getTitle());
        $htmlFrame->setCoverImagesIfNotEmpty($event->getCover());
        $htmlFrame->setDescriptionIfNotEmpty(
            (string) str($item->getDescription())->stripHtmlTags(),
            200,
        );
    }
}
