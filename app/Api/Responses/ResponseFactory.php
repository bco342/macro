<?php declare(strict_types=1);

namespace App\Api\Responses;

use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    public function __construct(
        private $response
    ) {}

    public function createResponse(array $data, int $code = 200): ResponseInterface
    {
        return new $this->response($data, $code);
    }


}