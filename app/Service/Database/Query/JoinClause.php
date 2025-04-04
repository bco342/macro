<?php

namespace App\Service\Database\Query;

class JoinClause
{
    public function __construct(
        public readonly string $select,
        public readonly string $sql
    ){}
}