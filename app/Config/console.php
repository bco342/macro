<?php

use App\Console\Command;
use App\Repository\AgencyRepository;
use App\Repository\AgencyRepositoryInterface;
use App\Repository\ContactRepository;
use App\Repository\ContactRepositoryInterface;
use App\Repository\EstateRepository;
use App\Repository\EstateRepositoryInterface;
use App\Repository\ManagerRepository;
use App\Repository\ManagerRepositoryInterface;
use App\Service\Importer\DataMapper;
use App\Service\Importer\DataProcessor;
use App\Service\Importer\ExcelReader;
use App\Service\Importer\ExcelReaderInterface;
use App\Service\Importer\Importer;
use App\Service\Migrator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

return [
    // Расположение миграций
    'migrations.dir' => DI\string(__DIR__ . '/../Migration'),

    // Регистрация сервисов
    Migrator::class => DI\autowire()
        ->constructorParameter('migrationsDir', DI\get('migrations.dir')),
    Importer::class => DI\autowire(),
    ExcelReaderInterface::class => DI\autowire(ExcelReader::class),
    DataProcessor::class => DI\autowire(),

    // Регистрация команд
    Command\ImportCommand::class => DI\autowire(),
    Command\MigrateCommand::class => DI\autowire(),

    'excelImportMappings' => require('import.php'),
    DataMapper::class => DI\autowire()
        ->constructorParameter('mappings', DI\get('excelImportMappings')),

    // Репозитории
    AgencyRepositoryInterface::class => DI\autowire(AgencyRepository::class),
    ManagerRepositoryInterface::class => DI\autowire(ManagerRepository::class),
    ContactRepositoryInterface::class => DI\autowire(ContactRepository::class),
    EstateRepositoryInterface::class => DI\autowire(EstateRepository::class),

    LoggerInterface::class => DI\autowire(NullLogger::class)
];
