<?php

namespace App\Service\Database\Query;

class InsertClause
{
    public function __construct(
        public readonly string $columns,
        public readonly string $placeholders
    ) {}

}