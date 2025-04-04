<?php declare(strict_types=1);

namespace App\Api;

use App\Api\Controllers\ControllerFactory;
use App\Api\Responses\ResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Log\LoggerInterface;

class Application
{
    private $dispatcher;
    private $emitter;

    public function __construct(
        private ContainerInterface            $container,
        private ServerRequestFactoryInterface $requestFactory,
        private ControllerFactory             $controllerFactory,
        private ResponseFactory               $responseFactory,
        private LoggerInterface               $logger
    )
    {
        $this->dispatcher = $this->container->get('RouteDispatcher');
        $this->emitter = $this->container->get('Emitter');
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function run(): void
    {
        try {
            $request = $this->requestFactory::fromGlobals();
            $routeInfo = $this->dispatcher->dispatch(
                $request->getMethod(),
                $request->getUri()->getPath()
            );

            switch ($routeInfo[0]) {
                case $this->dispatcher::NOT_FOUND:
                    $responseBody = ['error' => 'Not found'];
                    $statusCode = 404;
                    break;
                case $this->dispatcher::METHOD_NOT_ALLOWED:
                    $responseBody = ['error' => 'Method not allowed'];
                    $statusCode = 405;
                    break;
                case $this->dispatcher::FOUND:
                    [$controllerClass, $method] = $routeInfo[1];
                    $controller = $this->controllerFactory->createController($controllerClass);
                    $responseBody = $controller?->$method($request);
                    $statusCode = $controller?->getStatusCode();
                    if (!$responseBody) {
                        throw new \Exception('Internal server error');
                    }
                    break;
            }

        } catch (\Throwable $e) {
            $responseBody = ['error' => $e->getMessage()];
            $statusCode = 500;
            $this->logger->error($e->getMessage());
        }

        $response = $this->responseFactory->createResponse($responseBody, $statusCode);
        $this->emitter->emit($response);
    }
}