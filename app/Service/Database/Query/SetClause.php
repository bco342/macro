<?php

namespace App\Service\Database\Query;

class SetClause
{
    public function __construct(
        public readonly string $sql
    ) {}
}