<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Manager;
use App\Service\Database\Query\ModelRelation;

class ManagerRepository extends BaseRepository implements ManagerRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private $mapRules = [
        'agency' => 'agency_id'
    ];

    public function getTableName(): string
    {
        return 'manager';
    }

    public function getModelClass(): string
    {
        return Manager::class;
    }

    public function getRelations(): array
    {
        return [
            new ModelRelation(
                targetTable: 'agency',
                joinType: ModelRelation::LEFT,
                conditions: ['agency_id' => 'id'],
                targetColumnsToSelect: ['name']
            ),
        ];
    }

    public  function mapFilterToProperty($queryParam): ?string
    {
        return $this->mapRules[$queryParam] ?? null;
    }
}
