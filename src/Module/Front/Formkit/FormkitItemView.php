<?php

declare(strict_types=1);

namespace App\Module\Front\Formkit;

use App\Entity\Formkit;
use App\Repository\FormkitRepository;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewMetadata;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

#[ViewModel(
    layout: 'formkit-item',
    js: 'formkit-item.js'
)]
class FormkitItemView implements ViewModelInterface
{
    public function __construct(
        protected ORM $orm,
        #[Autowire] protected FormkitRepository $repository
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
    public function prepare(AppContext $app, View $view): array
    {
        $alias = $app->input('alias');

        /** @var Formkit $item */
        $item = $this->repository->mustGetItem(compact('alias'));

        if (!$item->getState()->isPublished()) {
            throw new RouteNotFoundException();
        }

        if (
            ($item->getPublishUp() && $item->getPublishUp()->isFuture())
            || ($item->getPublishDown() && $item->getPublishUp()->isPast())
        ) {
            throw new RouteNotFoundException('Not in published time.');
        }

        $view[$item::class] = $item;

        return compact('item');
    }

    #[ViewMetadata]
    public function prepareMetadata(HtmlFrame $htmlFrame, Formkit $item): void
    {
        $htmlFrame->setTitle($item->getTitle());
    }
}
