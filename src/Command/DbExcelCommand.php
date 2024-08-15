<?php

declare(strict_types=1);

namespace App\Command;

use Lyrasoft\Toolkit\Spreadsheet\PhpSpreadsheetWriter;
use Lyrasoft\Toolkit\Spreadsheet\SpreadsheetKit;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Manager\DatabaseManager;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Filesystem\Path;

#[CommandWrapper(
    description: ''
)]
class DbExcelCommand implements CommandInterface
{
    public function __construct(protected DatabaseManager $databaseManager, protected ApplicationInterface $app)
    {
    }

    /**
     * configure
     *
     * @param  Command  $command
     *
     * @return  void
     */
    public function configure(Command $command): void
    {
        $command->addOption(
            'connection',
            'c',
            InputOption::VALUE_REQUIRED,
            'The db connection to use',
            null
        );

        $command->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'The output path',
            null
        );
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
        $output = $io->getArgument('output');
        $outputName = sprintf(
            'DbSchema-%s.xlsx',
            $this->app->getAppName(),
        );

        if (!$output) {
            $output = 'tmp/' . $outputName;
        }

        $dir = dirname($output);
        Filesystem::mkdir($dir);

        if (is_dir($output)) {
            $output .= '/' . $outputName;
        } else {
            $outputName = Path::getFilename($output);
        }

        $conn = $io->getOption('connection');
        $db = $this->databaseManager->get($conn);

        $excel = SpreadsheetKit::createPhpSpreadsheetWriter();
        /** @var Worksheet $sheet */
        $sheet = $excel->setActiveSheet(0);
        $sheet->setTitle('Summary');

        $excel->addColumn('table', 'Table')->setWidth(15);
        $tables = $db->getSchema()->getTables();

        foreach ($tables as $table) {
            $excel->addRow(
                function (PhpSpreadsheetWriter $row) use ($table) {
                    $row->setRowCell('table', $table->tableName);
                }
            );
        }

        foreach ($tables as $table) {
            //
        }

        $excel->save($output, 'xlsx');

        $io->writeln('[Export to] ' . $output);

        return 0;
    }
}
