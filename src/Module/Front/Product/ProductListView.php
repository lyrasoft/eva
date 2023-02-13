<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Product;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Module\Front\Category\CategoryViewTrait;
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\Repository\ProductRepository;
use Lyrasoft\ShopGo\Traits\CurrencyAwareTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

use function Windwalker\Query\val;
use function Windwalker\str;

/**
 * The ProductListView class.
 */
#[ViewModel(
    layout: 'product-list',
    js: 'product-list.js'
)]
class ProductListView implements ViewModelInterface
{
    use CategoryViewTrait;
    use TranslatorTrait;
    use CurrencyAwareTrait;

    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected ProductRepository $repository,
        protected UserService $userService,
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app   The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        // Prepare Items
        $q = $app->input('q');
        $page = $app->input('page');
        $path = $app->input('path');

        $category = $this->getCategory(['type' => 'article', 'path' => $path]);

        $limit = 16;
        $user = $this->userService->getUser();

        $items = $this->repository->getFrontListSelector($user)
            ->addFilter('category_id', $category?->getId())
            ->searchTextFor(
                $q ?? '',
                $this->getSearchFields()
            )
            ->tapIf(
                (bool) $category,
                fn (ListSelector $selector) => $selector->leftJoin(
                    ShopCategoryMap::class,
                    'category_map',
                    [
                        ['category_map.type', '=', val('product')],
                        ['category_map.target_id', '=', 'product.id'],
                        ['category_map.category_id', '=', val($category->getId())],
                    ]
                )
            )
            ->ordering(
                $category
                    ? 'category_map.ordering ASC'
                    : 'product.modified DESC'
            )
            ->page($page)
            ->limit($limit);

        $pagination = $items->getPagination();

        $this->prepareMetadata($app, $view);

        return compact(
            'items',
            'pagination',
            'category',
            'q',
        );
    }

    protected function prepareMetadata(AppContext $app, View $view, ?Category $category = null): void
    {
        $htmlFrame = $view->getHtmlFrame();

        if ($category) {
            $htmlFrame->setTitle($category->getTitle());
            $htmlFrame->setDescription(
                (string) str($category->getDescription())->stripHtmlTags()->truncate(200, '...')
            );
        } else {
            $htmlFrame->setTitle($this->trans('shopgo.product.list.title'));
        }
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'product.id',
            'product.title',
            'product.alias',
            'product.model',
            'product.search_index',
        ];
    }
}
