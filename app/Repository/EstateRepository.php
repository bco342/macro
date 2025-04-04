<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Estate;
use App\Service\Database\Query\ModelRelation;

class EstateRepository extends BaseRepository implements EstateRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private static $mapRules = [
        'agency' => 'agency_id',
        'manager' => 'manager_id',
        'contact' => 'contact_id',
    ];

    public static function mapFilterToProperty(string $queryParam): ?string
    {
        return static::$mapRules[$queryParam] ?? null;
    }

    public static function getRelations(): array
    {
        return [
            AgencyRepository::class => new ModelRelation(
                joinType: ModelRelation::LEFT,
                conditions: ['agency_id' => 'id'],
                excludes: ['id']
            ),
            ManagerRepository::class => new ModelRelation(
                joinType: ModelRelation::LEFT,
                conditions: ['manager_id' => 'id'],
                excludes: ['id', 'agency_id']
            ),
            ContactRepository::class => new ModelRelation(
                joinType: ModelRelation::LEFT,
                conditions: ['contact_id' => 'id'],
                excludes: ['id', 'agency_id']
            )
        ];
    }

    public static function getTableName(): string
    {
        return 'estate';
    }

    public static function getModelClass(): string
    {
        return Estate::class;
    }
}
