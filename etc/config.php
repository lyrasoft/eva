<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2020 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

declare(strict_types=1);

return array_merge(
    [
        'app'      => include __DIR__ . '/conf/app.php',
        'asset'    => include __DIR__ . '/conf/asset.php',
        'auth'     => include __DIR__ . '/conf/auth.php',
        'session'  => include __DIR__ . '/conf/session.php',
        'logs'     => include __DIR__ . '/conf/logs.php',
        'error'    => include __DIR__ . '/conf/error.php',
        'whoops'   => include __DIR__ . '/conf/whoops.php',
        'events'   => include __DIR__ . '/conf/events.php',
        'database' => include __DIR__ . '/conf/database.php',
        'mail'     => include __DIR__ . '/conf/mail.php',
        'routing'  => include __DIR__ . '/conf/routing.php',
        'cache'    => include __DIR__ . '/conf/cache.php',
        'security' => include __DIR__ . '/conf/security.php',
        'renderer' => include __DIR__ . '/conf/renderer.php',
        'form'     => include __DIR__ . '/conf/form.php',
        'language' => include __DIR__ . '/conf/language.php',
        // 'queue' => include __DIR__ . '/conf/queue.php',

        'di' => include __DIR__ . '/di.php',
    ],

    \Windwalker\include_arrays(__DIR__ . '/packages/*.php'),

    // Load custom values
    is_file(__DIR__ . '/conf/custom.php') ? require __DIR__ . '/conf/custom.php' : []
);
