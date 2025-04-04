<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\Contact;
use App\Service\Database\Query\ModelRelation;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    // массив разрешенных фильтров [queryParam => sql-колонка]
    private static $mapRules = [
        'agency' => 'agency_id'
    ];

    public static function getTableName(): string
    {
        return 'contacts';
    }

    public static function getModelClass(): string
    {
        return Contact::class;
    }

    public static function getRelations(): array
    {
        return [
            AgencyRepository::class => new ModelRelation(
                ModelRelation::LEFT,
                ['agency_id' => 'id'],
                ['id']
            ),
        ];
    }

    public static function mapFilterToProperty($queryParam): ?string
    {
        return static::$mapRules[$queryParam] ?? null;
    }
}
