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
use Lyrasoft\ShopGo\Entity\Wishlist;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Attributes\JsonApi;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\ORM\ORM;

/**
 * The WishlistController class.
 */
#[Controller(
    config: __DIR__ . '/wishlist.config.php'
)]
class WishlistController
{
    #[JsonApi]
    public function ajax(AppContext $app): mixed
    {
        $task = $app->input('task');

        return $app->call([$this, $task]);
    }

    public function addWishlist(AppContext $app, ORM $orm, UserService $userService): Wishlist
    {
        $id = $app->input('id');
        $type = $app->input('type');

        $user = $userService->getUser();

        if (!$user->isLogin()) {
            throw new UnauthorizedException('請先登入', 401);
        }

        $wishlist = new Wishlist();
        $wishlist->setUserId($user->getId());
        $wishlist->setProductId((int) $id);

        $wishlist = $orm->createOne(Wishlist::class, $wishlist);

        $app->addMessage('已加入待買清單');

        return $wishlist;
    }

    public function removeWishlist(AppContext $app, ORM $orm, UserService $userService): bool
    {
        $id = $app->input('id');

        $user = $userService->getUser();

        if (!$user->isLogin()) {
            throw new UnauthorizedException('請先登入', 401);
        }

        $orm->deleteWhere(Wishlist::class, ['product_id' => $id, 'user_id' => $user->getId()]);

        $app->addMessage('已移除待買清單');

        return true;
    }
}
