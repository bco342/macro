<?php

namespace App\Repository;

interface TableMetadataInterface {
    public static function getTableName(): string;
    public static function getModelClass(): string;
    public static function getModelProperties(): array;
}