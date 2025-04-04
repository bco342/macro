<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Estate;
use App\Service\Database\Query\ModelRelation;

class EstateRepository extends BaseRepository implements EstateRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private $mapRules = [
        'agency' => 'agency_id',
        'manager' => 'manager_id',
        'contact' => 'contact_id',
    ];

    public function mapFilterToProperty(string $queryParam): ?string
    {
        return $this->mapRules[$queryParam] ?? null;
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
            new ModelRelation(
                targetTable: 'manager',
                joinType: ModelRelation::LEFT,
                conditions: ['manager_id' => 'id'],
                targetColumnsToSelect: ['name']
            ),
            new ModelRelation(
                targetTable: 'contacts',
                joinType: ModelRelation::LEFT,
                conditions: ['contact_id' => 'id'],
                targetColumnsToSelect: ['name', 'phones']
            ),
        ];
    }

    public function getTableName(): string
    {
        return 'estate';
    }

    public function getModelClass(): string
    {
        return Estate::class;
    }
}
