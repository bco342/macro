<?php

namespace App\Repository;


use App\Model\ModelInterface;

interface RepositoryInterface
{
    public function processData(array $attributes, array $conditions): ModelInterface;

    public function findOne(array $conditions): ModelInterface|false;

    public function create(array $attributes): ModelInterface;

    public function update(array $attributes): ModelInterface;

    public function countAll(array $queryParams = []): int;

    public function findFiltered(int $offset, int $limit, array $queryParams = []);
}