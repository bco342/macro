<?php declare(strict_types=1);

namespace App\Service\Importer;

use App\Repository\AgencyRepositoryInterface;
use App\Repository\ContactRepositoryInterface;
use App\Repository\EstateRepositoryInterface;
use App\Repository\ManagerRepositoryInterface;

class DataProcessor
{
    private static array $headers;

    public function __construct(
        private \PDO                       $connection,
        private AgencyRepositoryInterface  $agencyRepository,
        private ManagerRepositoryInterface $managerRepository,
        private ContactRepositoryInterface $contactRepository,
        private EstateRepositoryInterface  $estateRepository,
        private DataMapper                 $mapper,
    ) {}

    /**
     * @param $row
     * @return void
     * @throws \Throwable
     */
    public function processRow($row): void
    {
        if (empty(static::$headers)) {
            $this->mapper->validateHeaders($row);
            static::$headers = $row;
            return;
        }
        $dataWithHeaders = array_combine(static::$headers, $row);

        $mappedData = $this->mapper->mapData($dataWithHeaders);

        $this->saveData($mappedData);
    }

    /**
     * @param array $rowData
     * @return void
     * @throws \Throwable
     */
    private function saveData(array $rowData): void
    {
        try {
            $this->connection->beginTransaction();

            $agency = $this->agencyRepository->processData($rowData['agency'], $rowData['agency']);

            $rowData['manager']['agency_id'] = $agency->getId();
            $manager = $this->managerRepository->processData($rowData['manager'], $rowData['manager']);

            $rowData['contact']['agency_id'] = $agency->getId();
            $contact = $this->contactRepository->processData($rowData['contact'], $rowData['contact']);

            $this->estateRepository->processData([
                'contact_id' => $contact->getId(),
                'manager_id' => $manager->getId(),
                'agency_id' => $agency->getId(),
                ...$rowData['estate']
            ], [
                'external_id' => $rowData['estate']['external_id']
            ]);

            $this->connection->commit();

        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}