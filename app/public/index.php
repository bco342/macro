<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Api\Application;
use DI\ContainerBuilder;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

$containerBuilder = new ContainerBuilder();
if (($_ENV['APP_ENV'] ?? 'dev') === 'prod') {
    error_reporting(0);
    $containerBuilder->enableCompilation(__DIR__ . '/../../var/cache/');
}
$containerBuilder->addDefinitions(__DIR__ . '/../Config/db.php');
$containerBuilder->addDefinitions(__DIR__ . '/../Config/api.php');
$container = $containerBuilder->build();

$application = $container->get(Application::class);
$application->run();