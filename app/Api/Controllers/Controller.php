<?php declare(strict_types=1);

namespace App\Api\Controllers;

abstract class Controller
{
    protected const API_VERSION = '1.0.0';
    protected $statusCode = 200;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}