<?php declare(strict_types=1);

namespace App\Service\Importer;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Importer
{
    public function __construct(
        private ExcelReaderInterface $reader,
        private DataProcessor        $processor,
        private LoggerInterface      $logger
    ) {
    }

    public function execute($file, OutputInterface $output): void
    {
        $output->writeln("<info>Start data import from file: " . $file . "</info>");
        $this->reader->open($file);
        $progressBar = new ProgressBar($output, $this->reader->getRowCount());
        $progressBar->start();

        try {
            foreach ($this->reader->read() as $row) {
                $this->processor->processRow($row);
                $progressBar->advance();
            }
            $progressBar->finish();
            $output->writeln('');

        } catch (\Throwable $e) {
            $message = "Import failed: " . $e->getMessage() . $e->getTraceAsString();
            $this->logger->error($message, [
                'file' => $file,
                'trace' => $e->getTrace()
            ]);
            throw new RuntimeException($message);
        } finally {
            $output->writeln('');
        }
    }
}
