<?php

namespace App\Repository;

interface TableMetadataInterface {
    public function getTableName(): string;
    public function getModelClass(): string;
}