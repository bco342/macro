<?php declare(strict_types=1);

namespace App\Console;

use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use DI\Container;

class Application extends SymfonyConsoleApplication
{
    public function __construct(
        private Container $container,
        private string $name = 'Estate Manager',
        private string $version = '1.0.0'
    ) {
        parent::__construct($name, $version);
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->addCommands([
            $this->container->get(Command\ImportCommand::class),
            $this->container->get(Command\MigrateCommand::class),
        ]);
    }
}
