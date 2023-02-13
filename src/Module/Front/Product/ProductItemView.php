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
use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Entity\AdditionalPurchase;
use Lyrasoft\ShopGo\Entity\AdditionalPurchaseMap;
use Lyrasoft\ShopGo\Entity\Discount;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Entity\Shipping;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\ShopGo\Entity\Wishlist;
use Lyrasoft\ShopGo\Enum\DiscountType;
use Lyrasoft\ShopGo\Repository\ProductRepository;
use Lyrasoft\ShopGo\Service\ProductAttributeService;
use Lyrasoft\ShopGo\Service\VariantService;
use Lyrasoft\ShopGo\Traits\CurrencyAwareTrait;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

use Windwalker\Query\Query;

use function Windwalker\collect;
use function Windwalker\str;

/**
 * The ProductItemView class.
 */
#[ViewModel(
    layout: 'product-item',
    js: 'product-item.js'
)]
class ProductItemView implements ViewModelInterface
{
    use TranslatorTrait;
    use CurrencyAwareTrait;

    /**
     * Constructor.
     */
    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected ProductRepository $repository,
        protected UserService $userService,
        protected VariantService $variantService,
        protected ProductAttributeService $productAttributeService,
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
        [$id, $alias, $preview] = $app->input('id', 'alias', 'preview')->values()->dump();

        /** @var Product $item */
        $item = $this->repository->mustGetItem($id);

        if ($item->getState()->isUnpublished()) {
            throw new RouteNotFoundException();
        }

        $variant = $this->orm->mustFindOne(ProductVariant::class, $item->getPrimaryVariantId());
        $category = $this->orm->mustFindOne(Category::class, $item->getCategoryId());

        // Prepare variant view & price
        $variant = $this->variantService->prepareVariantView($variant, $item);

        // Sub Variants
        $variants = $this->orm->from(ProductVariant::class)
            ->where('product_id', $item->getId())
            ->where('primary', '!=', 1)
            ->where('state', 1)
            ->all(ProductVariant::class);

        $minPrice = 0;
        $maxPrice = 0;

        /** @var ProductVariant $subVariant */
        foreach ($variants as $subVariant) {
            $minPrice = min($minPrice, $variant->getPrice());
            $maxPrice = max($maxPrice, $variant->getPrice());
        }

        // Features
        $features = $this->variantService->findFeaturesFromProduct($item);

        // Shippings
        $shippingIds = $item->getShippings();

        $shippings = $this->orm->from(Shipping::class)
            ->where('state', 1)
            ->where('id', $shippingIds ?: [0])
            ->all(Shipping::class);

        // Discounts
        $discounts = $this->orm->from(Discount::class)
            ->where('type', DiscountType::PRODUCT())
            ->where('subtype', 'discount')
            ->where('product_id', $item->getId())
            ->order('min_product_quantity', 'ASC')
            ->all(Discount::class);

        // Attributes
        /** @var Category[] $attrGroups */
        [$attributes, $attrGroups] = $this->productAttributeService->getAttributesAndGroupsWithValues(
            $item
        );

        $attributeSet = $attributes->groupBy('categoryId');

        foreach ($attrGroups as $group) {
            $params = $group->getParams();
            $params['attributes'] = $attributeSet[$group->getId()] ?? collect();

            $group->setParams($params);
        }

        // Additional Purchases
        $additionalPurchases = $this->orm->from(ProductVariant::class)
            ->leftJoin(Product::class)
            ->leftJoin(
                AdditionalPurchaseMap::class,
                'ap_map',
                'ap_map.attach_variant_id',
                'product_variant.id'
            )
            ->where('ap_map.target_product_id', $item->getId())
            ->groupByJoins()
            ->all(ProductVariant::class);

        // Tabs
        $tabs = $this->getTabsByCategoryId($category->getId());

        $this->prepareMetadata($app, $view, $item, $variant);

        // Wishlist
        $user = $this->userService->getUser();

        if ($user->isLogin()) {
            $wishlist = $this->orm->findOne(
                Wishlist::class,
                ['user_id' => $user->getId(), 'product_id' => $item->getId()]
            );
        } else {
            $wishlist = null;
        }

        return compact(
            'item',
            'variant',
            'category',
            'features',
            'discounts',
            'shippings',
            'attrGroups',
            'attributeSet',
            'tabs',
            'minPrice',
            'maxPrice',
            'additionalPurchases',
            'wishlist'
        );
    }

    protected function prepareMetadata(AppContext $app, View $view, Product $item, ProductVariant $variant): void
    {
        $asset = $app->service(AssetService::class);
        $view->setTitle($item->getTitle());

        $htmlFrame = $view->getHtmlFrame();
        $metadata = $item->getMeta();

        $htmlFrame->setDescription(
            (string) str($metadata->getDescription() ?: $item->getDescription())->stripHtmlTags()
                ->truncate(200, '...')
        );

        $images[] = $asset->addAssetBase($variant->getCover());

        foreach ($variant->getImages() as $image) {
            $images[] = $asset->addAssetBase($image['url']);
        }

        $htmlFrame->setCoverImages(...$images);
    }

    protected function getTabsByCategoryId(int $categoryId): Collection
    {
        return $this->orm->from(ProductTab::class, 'tab')
            ->orWhere(
                function (Query $query) use ($categoryId) {
                    $query->whereNotExists(
                        fn(Query $query) => $query->from(ShopCategoryMap::class)
                            ->whereRaw('type = %q', 'tab')
                            ->whereRaw('target_id = tab.id')
                    );

                    $query->whereExists(
                        fn(Query $query) => $query->from(ShopCategoryMap::class)
                            ->whereRaw('type = %q', 'tab')
                            ->whereRaw('category_id = %a', $categoryId)
                            ->whereRaw('target_id = tab.id')
                    );
                }
            )
            ->order('tab.ordering', 'ASC')
            ->all(ProductTab::class);
    }
}
