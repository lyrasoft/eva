<?php

declare(strict_types=1);

namespace App\Module\Front\Cart;

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Cart\CartStorage;
use Lyrasoft\ShopGo\Entity\Address;
use Lyrasoft\ShopGo\Entity\Location;
use Lyrasoft\ShopGo\ShopGoPackage;
use Psr\Cache\InvalidArgumentException;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\ORM\NestedSetMapper;
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
    use TranslatorTrait;

    /**
     * Constructor.
     */
    public function __construct(
        protected UserService $userService,
        protected Navigator $nav,
        protected ORM $orm,
        protected ShopGoPackage $shopGo,
        protected CartStorage $cartStorage,
    ) {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app   The web app context.
     * @param  View        $view  The view object.
     *
     * @return  RouteUri|array
     * @throws InvalidArgumentException
     */
    public function prepare(AppContext $app, View $view): RouteUri|array
    {
        $user = $this->userService->getUser();

        if (!$user->isLogin() && !$this->shopGo->config('checkout.allow_anonymous')) {
            return $this->nav->to('login')->withReturn();
        }

        if ($this->cartStorage->count() === 0) {
            return $this->nav->to('home');
        }

        $checkoutData = $app->state->getAndForget('checkout.data');

        /** @var NestedSetMapper $locationMapper */
        $locationMapper = $this->orm->mapper(Location::class);

        $defaultLocationId = $this->shopGo->config('shipping.default_location_id');

        if ($defaultLocationId) {
            $checkoutData['payment_data']['location_id'] = $defaultLocationId;
            $checkoutData['payment_data']['locationPath'] = $locationMapper->getPath(
                $checkoutData['payment_data']['location_id']
            )
                ->map(fn (Location $location) => $location->id)
                ->map('strval')
                ->slice(1)
                ->dump();
        }

        $this->prepareMetadata($app, $view);

        return compact('checkoutData');
    }

    protected function prepareMetadata(AppContext $app, View $view): void
    {
        $view->setTitle(
            $this->trans('shopgo.cart.title')
        );
    }
}
