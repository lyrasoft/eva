<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Wishlist;

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Repository\ProductRepository;
use Psr\Cache\InvalidArgumentException;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;
use Windwalker\Query\Query;

/**
 * The WishlistListView class.
 */
#[ViewModel(
    layout: 'wishlist-list',
    js: 'wishlist-list.js'
)]
class WishlistListView implements ViewModelInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected ProductRepository $repository,
        protected UserService $userService,
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
     * @throws InvalidArgumentException
     */
    public function prepare(AppContext $app, View $view): array
    {
        $page = $app->input('page');
        $user = $this->userService->getUser();

        $items = $this->repository->getFrontListSelector($user)
            ->where('favorite.id', '!=', null)
            ->page($page)
            ->all();

        return compact('items');
    }
}
