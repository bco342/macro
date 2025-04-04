<?php

namespace App\Service\Importer;

class ImportRule
{
    public function __construct(
        public readonly string $model,
        public readonly string $property,
        public readonly string|array $callback,
    ) {}

}