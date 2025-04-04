<?php

namespace App\Repository;

interface HasRelationsInterface
{
    public function getRelations(): array;

    public function getTableName(): string;

    public function getModelClass(): string;
}