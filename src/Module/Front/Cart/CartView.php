<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Cart;

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\Service\AddressService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;

use Windwalker\ORM\ORM;

use function Windwalker\collect;

/**
 * The CartView class.
 */
#[ViewModel(
    layout: 'cart',
    js: 'cart.js'
)]
class CartView implements ViewModelInterface
{
    /**
     * Constructor.
     */
    public function __construct(protected UserService $userService, protected ORM $orm)
    {
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
        $user = $this->userService->getUser();

        $addresses = collect();

        if ($user->isLogin()) {
            $addresses = $this->orm->from(Address::class)
                ->leftJoin(
                    Location::class,
                    'location',
                    'location.id',
                    'address.location_id'
                )
                ->where('address.user_id', $user->getId())
                ->order('address.id', 'DESC')
                ->groupByJoins()
                ->all(Address::class);

            /** @var Address $address */
            foreach ($addresses as $address) {
                $location = $this->orm->toEntity(Location::class, $address->location);
                $address->formatted = $address->formatByLocation($location, true);
            }
        }

        return compact(
            'addresses'
        );
    }
}
