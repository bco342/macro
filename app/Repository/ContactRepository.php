<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Contact;
use App\Service\Database\Query\ModelRelation;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private $mapRules = [
        'agency' => 'agency_id'
    ];

    public function getTableName(): string
    {
        return 'contacts';
    }

    public function getModelClass(): string
    {
        return Contact::class;
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

    public function mapFilterToProperty($queryParam): ?string
    {
        return $this->mapRules[$queryParam] ?? null;
    }
}
