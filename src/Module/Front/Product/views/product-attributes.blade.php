<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\ShopGo\Entity\ProductAttribute;
use Lyrasoft\ShopGo\Enum\ProductAttributeType;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var $attrGroups Category[]
 * @var $attribute  ProductAttribute
 */

?>

<table class="table table-bordered table-striped">
    <tbody>
    @foreach ($attrGroups as $attrGroup)
            <?php
            $attributes = $attrGroup->getParams()['attributes'] ?? [];

            if (!count($attributes)) {
                continue;
            }

            $hasValue = false;

            foreach ($attributes as $attribute) {
                $hasValue = $hasValue || (string) $attribute->getValue() !== '';
            }

            if (!$hasValue) {
                continue;
            }
            ?>

        <tr>
            <th class="text-bg-dark" colspan="5">
                {{ $attrGroup->getTitle() }}
            </th>
        </tr>

        @foreach ($attributes as $attribute)
            @if ($attribute->getValue() === '')
                @continue
            @endif
            <tr>
                <th style="width: 33%">
                    {{ $attribute->getTitle() }}
                </th>
                <td>
                    @if ($attribute->getType() === ProductAttributeType::BOOL())
                        {{ $attribute->getValue() ? '有' : '無' }}
                    @else
                        {{ $attribute->getValue() }}
                    @endif
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
