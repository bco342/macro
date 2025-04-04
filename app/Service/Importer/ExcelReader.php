<?php declare(strict_types=1);

namespace App\Service\Importer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReader implements ExcelReaderInterface
{
    private Worksheet $worksheet;

    public function open($file): void
    {
        $spreadsheet = IOFactory::load($file, IReader::READ_DATA_ONLY);
        $this->worksheet = $spreadsheet->getActiveSheet();
    }

    public function read(): \Generator
    {
        if (!$this->worksheet) {
            throw new \RuntimeException('Worksheet is not loaded');
        }

        return $this->worksheet->rangeToArrayYieldRows(
            $this->worksheet->calculateWorksheetDataDimension(),
            null,
            false,
            false
        );
    }

    public function getRowCount(): int
    {
        if (!$this->worksheet) {
            throw new \RuntimeException('Worksheet is not loaded');
        }

        return $this->worksheet->getHighestRow();
    }
}