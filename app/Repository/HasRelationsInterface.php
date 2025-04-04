<?php

namespace App\Repository;

interface HasRelationsInterface
{
    public static function getRelations(): array;

    public static function getTableName(): string;

    public static function getModelClass(): string;
}