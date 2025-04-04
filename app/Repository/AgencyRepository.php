<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Agency;

class AgencyRepository extends BaseRepository implements AgencyRepositoryInterface
{
    public static function getTableName(): string
    {
        return 'agency';
    }

    public static function getModelClass(): string
    {
        return Agency::class;
    }

    public static function mapFilterToProperty(string $queryParam): ?string
    {
        return null;
    }

    public static function getRelations(): array
    {
        return [];
    }
}
