<?php declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Service\Migrator;

class MigrateCommand extends Command
{
    public function __construct(
        private Migrator $migrator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate')
            ->setDescription('Database migrations')
            ->addArgument(
                'direction',
                InputArgument::REQUIRED,
                'Migration direction (up/down)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $direction = $input->getArgument('direction');

        try {
            if ($direction === 'up') {
                $this->migrator->migrateUp();
                $output->writeln('<info>Migrations applied successfully</info>');
            } elseif ($direction === 'down') {
                $this->migrator->migrateDown();
                $output->writeln('<info>Last migration reverted</info>');
            }
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Migration failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
