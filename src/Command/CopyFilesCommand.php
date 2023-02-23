<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;

use Windwalker\Filesystem\Filesystem;

use function Windwalker\fs;

/**
 * The CopyFilesCommand class.
 */
#[CommandWrapper(
    description: ''
)]
class CopyFilesCommand implements CommandInterface
{
    /**
     * configure
     *
     * @param  Command  $command
     *
     * @return  void
     */
    public function configure(Command $command): void
    {
        //
    }

    /**
     * Executes the current command.
     *
     * @param  IOInterface  $io
     *
     * @return  int Return 0 is success, 1-255 is failure.
     */
    public function execute(IOInterface $io): int
    {
        $migrations = Filesystem::files(WINDWALKER_MIGRATIONS);
        $files = [];

        foreach ($migrations as $migration) {
            [$id, $name] = explode('_', $migration->getBasename(), 2);

            $files[$id] = $migration;
        }

        ksort($files);

        $start = false;
        $destFolder = WINDWALKER_VENDOR . '/lyrasoft/shopgo/resources/migrations';

        foreach ($files as $file) {
            if ($file->getBasename() === '2022122708280001_ManufacturerInit.php') {
                $start = true;
            }

            if ($start) {
                $file->copyTo($dest = $destFolder . '/' . $file->getBasename(), true);
                $io->writeln('[Copy] ' . $dest);
            }
        }

        // Seeders
        $seeders = Filesystem::files(WINDWALKER_SEEDERS);
        $destFolder = WINDWALKER_VENDOR . '/lyrasoft/shopgo/resources/seeders';

        foreach ($seeders as $seeder) {
            if ($seeder->getBasename() === 'main.php') {
                continue;
            }

            $seeder->copyTo($dest = $destFolder . '/' . $seeder->getBasename(), true);
            $io->writeln('[Copy] ' . $dest);
        }

        return 0;
    }
}
