<?php

namespace App\Repository;

interface FilterableInterface
{
    public static function mapFilterToProperty(string $queryParam): ?string;

    public static function getTableName(): string;

}