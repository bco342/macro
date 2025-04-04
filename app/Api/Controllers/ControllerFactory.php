<?php declare(strict_types=1);

namespace App\Api\Controllers;

use Psr\Container\ContainerInterface;

class ControllerFactory
{
    public function __construct(
        private ContainerInterface $container
    ) {}

    public function createController($controllerName)
    {
        return $this->container->get($controllerName);
    }


}
