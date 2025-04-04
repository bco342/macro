<?php

namespace App\Service\Importer;

interface ExcelReaderInterface
{
    public function open($file): void;

    public function read(): \Generator;

    public function getRowCount(): int;
}