<?php

namespace App\Service\Database\Query;

class WhereClause
{
    public function __construct(
        public readonly string $sql,
        public readonly array  $params = []
    ) {}
}