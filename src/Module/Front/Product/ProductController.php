<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Product;

use Lyrasoft\ShopGo\Entity\Discount;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Enum\DiscountType;
use Lyrasoft\ShopGo\Service\VariantService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Attributes\JsonApi;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
use Windwalker\ORM\ORM;

/**
 * The ProductController class.
 */
#[Controller()]
class ProductController
{
    use TranslatorTrait;

    #[JsonApi]
    public function ajax(AppContext $app): mixed
    {
        $task = $app->input('task');

        return $app->call([$this, $task]);
    }

    public function getVariant(AppContext $app, ORM $orm, VariantService $variantService): array
    {
        $options = (array) $app->input('options');
        $productId = (int) $app->input('product_id');

        $hash = VariantService::hash($options);

        /** @var ProductVariant $variant */
        $variant = $orm->from(ProductVariant::class)
            ->where('product_id', $productId)
            ->where('hash', $hash)
            ->get(ProductVariant::class);

        if (!$variant) {
            return [
                'variant' => null,
                'discounts' => []
            ];
        }

        $variant = $variantService->prepareVariantView($variant);

        return [
            'variant' => $variant,
        ];
    }
}
