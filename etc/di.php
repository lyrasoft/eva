<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2020 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

use Windwalker\Utilities\Arr;

use function Windwalker\include_arrays;

return Arr::mergeRecursive(
// Load with namespace,
    [
        'factories' => include_arrays(__DIR__ . '/di/*.php'),
        'providers' => [
            //
        ],
        'bindings' => [
            //
        ],
        'aliases' => [
            //
        ],
        'layouts' => [
            //
        ],
        'attributes' => [
            //
        ],
    ]
);
