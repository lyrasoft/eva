<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Portfolio;

use App\Entity\Portfolio;
use App\Repository\PortfolioRepository;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Repository\CategoryRepository;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;

use function Windwalker\collect;
use function Windwalker\str;

/**
 * The PortfolioItemView class.
 */
#[ViewModel(
    layout: 'portfolio-item',
    js: 'portfolio-item.js'
)]
class PortfolioItemView implements ViewModelInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        #[Autowire]
        protected PortfolioRepository $repository,
        #[Autowire]
        protected CategoryRepository $categoryRepository,
        protected Navigator $nav
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

        /** @var Portfolio $item */
        $item = $this->repository->getItem($id);

        if (!$item || $item->getState()->equals(BasicState::UNPUBLISHED())) {
            throw new RouteNotFoundException('Item not found.');
        }

        /** @var Category $category */
        $category = $this->categoryRepository->getItem($item->getCategoryId());

        if (!$category || $category->getState()->equals(BasicState::UNPUBLISHED())) {
            throw new RouteNotFoundException('Category not published.');
        }

        // Keep URL unique
        if ($item->getAlias() !== $alias) {
            return $this->nav->self()->alias($item->getAlias());
        }

        $this->prepareMetadata($view->getHtmlFrame(), $item);

        return compact(
            'item',
            'category'
        );
    }

    protected function prepareMetadata(HtmlFrame $htmlFrame, Portfolio $item): void
    {
        $meta = collect($item->getMeta());

        $htmlFrame->setTitle($meta->title ?: $item->getTitle());
        $htmlFrame->setCoverImagesIfNotEmpty($item->getCover());
        $htmlFrame->setDescriptionIfNotEmpty(
            $meta->description
            ?: (string) str($item->getDescription())->stripHtmlTags()->truncate(150, '...')
        );

        if ($meta->keyword) {
            $htmlFrame->addMetadata('keyword', (string) $meta->keyword);
        }
    }
}
