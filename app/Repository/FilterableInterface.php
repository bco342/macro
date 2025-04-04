<?php

namespace App\Repository;

interface FilterableInterface
{
    public function mapFilterToProperty(string $queryParam): ?string;

    public function getTableName(): string;

}