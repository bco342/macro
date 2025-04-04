<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Agency;

class AgencyRepository extends BaseRepository implements AgencyRepositoryInterface
{
    public function getTableName(): string
    {
        return 'agency';
    }

    public function getModelClass(): string
    {
        return Agency::class;
    }

    public function mapFilterToProperty(string $queryParam): ?string
    {
        return null;
    }

    public function getRelations(): array
    {
        return [];
    }
}
