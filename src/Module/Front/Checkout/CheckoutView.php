<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Front\Checkout;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Utilities\TypeCast;

use function Windwalker\count;

/**
 * The CheckoutView class.
 */
#[ViewModel(
    layout: [
        'checkout',
        'shipping',
        'payment',
    ],
    js: 'checkout.js'
)]
class CheckoutView implements ViewModelInterface
{
    /**
     * Constructor.
     */
    public function __construct()
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
        return [];
    }

    public function flattenAsInputName(
        object|array $array,
        ?string $prefix = null
    ): array {
        $temp = [];

        foreach (TypeCast::toArray($array, false) as $k => $v) {
            $key = $prefix !== null ? $prefix . "[$k]" : $k;

            if (is_array($v)) {
                $temp[] = $this->flattenAsInputName($v, (string) $key);
            } else {
                $temp[] = [$key => $v];
            }
        }

        // Prevent resource-greedy loop.
        // @see https://github.com/dseguy/clearPHP/blob/master/rules/no-array_merge-in-loop.md
        if (count($temp)) {
            return array_merge(...$temp);
        }

        return [];
    }
}
