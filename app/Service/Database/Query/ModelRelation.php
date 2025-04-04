<?php

namespace App\Service\Database\Query;

class ModelRelation
{
    public const string LEFT = 'LEFT';
    public const string RIGHT = 'RIGHT';
    public const string INNER = 'INNER';

    public function __construct(
        public readonly string $targetTable,
        public readonly array $targetColumnsToSelect,
        public readonly string $joinType,
        public readonly array $conditions
    ) {}

    public static function getTypes()
    {
        return [
            static::LEFT,
            static::RIGHT,
            static::INNER,
        ];
    }
}