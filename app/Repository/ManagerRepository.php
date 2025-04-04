<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Manager;
use App\Service\Database\Query\ModelRelation;

class ManagerRepository extends BaseRepository implements ManagerRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private static $mapRules = [
        'agency' => 'agency_id'
    ];

    public static function getTableName(): string
    {
        return 'manager';
    }

    public static function getModelClass(): string
    {
        return Manager::class;
    }

    public static function getRelations(): array
    {
        return [
            AgencyRepository::class => new ModelRelation(
                joinType: ModelRelation::LEFT,
                conditions: ['agency_id' => 'id'],
                excludes: ['id']
            )
        ];
    }

    public static function mapFilterToProperty($queryParam): ?string
    {
        return static::$mapRules[$queryParam] ?? null;
    }
}
