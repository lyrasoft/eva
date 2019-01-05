<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2014 {ORGANIZATION}. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

use Symfony\Component\Dotenv\Dotenv;

define('WINDWALKER_ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define('WINDWALKER_BIN', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'bin');
define('WINDWALKER_CACHE', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'cache');
define('WINDWALKER_ETC', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'etc');
define('WINDWALKER_LOGS', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'logs');
define('WINDWALKER_RESOURCES', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'resources');
define('WINDWALKER_SOURCE', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'src');
define('WINDWALKER_TEMP', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'tmp');
define('WINDWALKER_TEMPLATES', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'templates');
define('WINDWALKER_VENDOR', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'vendor');
define('WINDWALKER_PUBLIC', WINDWALKER_ROOT . DIRECTORY_SEPARATOR . 'www');

define('WINDWALKER_MIGRATIONS', WINDWALKER_RESOURCES . DIRECTORY_SEPARATOR . 'migrations');
define('WINDWALKER_SEEDERS', WINDWALKER_RESOURCES . DIRECTORY_SEPARATOR . 'seeders');
define('WINDWALKER_LANGUAGES', WINDWALKER_RESOURCES . DIRECTORY_SEPARATOR . 'languages');

$env = WINDWALKER_ROOT . '/.env';

if (is_file($env)) {
    (new Dotenv())->load($env);
}
