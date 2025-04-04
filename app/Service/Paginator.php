<?php declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\RequestInterface;

class Paginator
{
    private $currentPage;
    private $perPage;
    private $totalItems;

    public function init(RequestInterface $request, int $defaultPerPage = 10, int $maxPerPage = 100): bool
    {
        $query = $request->getQueryParams();

        $this->currentPage = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        $this->perPage = filter_var($query['per_page'] ?? $defaultPerPage, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => $maxPerPage]
        ]);

        return $this->currentPage !== false && $this->perPage !== false;
    }

    public function setTotalItems(int $totalItems): bool
    {
        $this->totalItems = $totalItems;

        return $totalItems !== 0 && $this->currentPage <= $this->getTotalPages();
    }

    public function getTotalPages(): int
    {
        static $value;
        if (!$value) {
            $value = (int)ceil($this->totalItems / $this->perPage);
        }
        return $value;
    }

    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function getLimit(): int
    {
        return $this->perPage;
    }

    public function getMeta(): array
    {
        return [
            'current_page' => $this->currentPage,
            'per_page'     => $this->perPage,
            'total_pages'  => $this->getTotalPages(),
            'total_items'  => $this->totalItems,
        ];
    }
}