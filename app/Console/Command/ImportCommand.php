<?php declare(strict_types=1);

namespace App\Console\Command;

use App\Service\Importer\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    public function __construct(
        private Importer $importer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('import')
            ->setDescription('Import data from Excel file')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to Excel file',
                $_ENV['EXCEL_PATH'] ?: '../data/estate.xlsx'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = $input->getArgument('file');
            $this->importer->execute($file, $output);
            $output->writeln('<info>Data imported successfully</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
