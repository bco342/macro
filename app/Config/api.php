<?php

use App\Api\Controllers\AgencyController;
use App\Api\Controllers\ContactController;
use App\Api\Controllers\ControllerFactory;
use App\Api\Controllers\EstateController;
use App\Api\Controllers\ManagerController;
use App\Api\Responses\ResponseFactory;
use App\Repository\AgencyRepository;
use App\Repository\AgencyRepositoryInterface;
use App\Repository\ContactRepository;
use App\Repository\ContactRepositoryInterface;
use App\Repository\EstateRepository;
use App\Repository\EstateRepositoryInterface;
use App\Repository\ManagerRepository;
use App\Repository\ManagerRepositoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

return [
    // Маршруты
    'RouteDispatcher' => DI\factory(function () {
        return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/api/agencies', [AgencyController::class, 'list']);
            $r->addRoute('GET', '/api/contacts', [ContactController::class, 'list']);
            $r->addRoute('GET', '/api/estates',  [EstateController::class, 'list']);
            $r->addRoute('GET', '/api/managers', [ManagerController::class, 'list']);
        });
    }),

    ServerRequestFactoryInterface::class => DI\autowire(Laminas\Diactoros\ServerRequestFactory::class),

    ResponseFactory::class => DI\autowire(App\Api\Responses\ResponseFactory::class)
        ->constructorParameter('response', DI\string(App\Api\Responses\XMLResponse::class)),

    'Emitter' => DI\autowire(Laminas\HttpHandlerRunner\Emitter\SapiEmitter::class),

    // Репозитории
    AgencyRepositoryInterface::class => DI\autowire(AgencyRepository::class),
    ManagerRepositoryInterface::class => DI\autowire(ManagerRepository::class),
    ContactRepositoryInterface::class => DI\autowire(ContactRepository::class),
    EstateRepositoryInterface::class => DI\autowire(EstateRepository::class),

    LoggerInterface::class => DI\autowire(NullLogger::class)
];
