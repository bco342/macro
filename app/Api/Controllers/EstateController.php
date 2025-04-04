<?php declare(strict_types=1);

namespace App\Api\Controllers;

use App\Repository\EstateRepositoryInterface;
use App\Service\Paginator;
use Laminas\Diactoros\ServerRequest;

class EstateController extends Controller
{
    const MAX_PER_PAGE = 100;
    const DEFAULT_PER_PAGE = 20;

    public function __construct(
        private EstateRepositoryInterface $repository,
        private Paginator $paginator
    ) {}

    public function list(ServerRequest $request): array
    {
        if (!$this->paginator->init($request, self::DEFAULT_PER_PAGE, self::MAX_PER_PAGE)) {
            $this->setStatusCode(400);
            return ['error' => 'Invalid pagination parameters'];
        }

        $totalItems = $this->repository->countAll($request->getQueryParams());

        if (!$this->paginator->setTotalItems($totalItems)) {
            $this->setStatusCode(404);
            return ['error' => 'Page not found'];
        }

        return [
            'meta' => [
                'version'      => self::API_VERSION,
                ...$this->paginator->getMeta(),
            ],
            'data' => [
                'estates' => $this->repository->findFiltered(
                    $this->paginator->getOffset(),
                    $this->paginator->getLimit(),
                    $request->getQueryParams(),
                )
            ]
        ];
    }
}