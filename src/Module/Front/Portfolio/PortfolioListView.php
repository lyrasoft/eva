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
use Lyrasoft\Luna\Module\Front\Category\CategoryViewTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;

/**
 * The PortfolioListView class.
 */
#[ViewModel(
    layout: 'portfolio-list',
    js: 'portfolio-list.js'
)]
class PortfolioListView implements ViewModelInterface
{
    use CategoryViewTrait;

    /**
     * Constructor.
     */
    public function __construct(
        #[Autowire]
        protected PortfolioRepository $repository,
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
        $path = $app->input('path');
        $category = $this->getCategoryOrFail('portfolio', $path);

        $limit = 10;
        $page = $app->input('page');

        $items = $this->repository->getListSelector()
            ->addFilter('portfolio.state', 1)
            ->addFilter('category.state', 1)
            ->where('category.lft', '>=', $category->getLft())
            ->where('category.rgt', '<=', $category->getRgt())
            ->ordering('portfolio.created', 'DESC')
            ->page($page)
            ->limit($limit);

        $pagination = $items->getPagination();

        $items = $items->getIterator(Portfolio::class);

        return compact('items', 'pagination');
    }

    public function prepareItem(Collection $item): object
    {
        return $this->repository->getEntityMapper()->toEntity($item);
    }
}
