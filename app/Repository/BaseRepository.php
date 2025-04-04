<?php declare(strict_types=1);

namespace App\Repository;

use App\Model\ModelInterface;
use App\Repository\Exception\CreateFailedException;
use App\Repository\Exception\UpdateFailedException;
use App\Service\Database\QueryBuilder;
use App\Service\Database\RelationManager;
use PDO;

abstract class BaseRepository
    implements RepositoryInterface, TableMetadataInterface, FilterableInterface, HasRelationsInterface
{
    public function __construct(
        protected PDO $connection,
        protected QueryBuilder $queryBuilder,
        protected RelationManager $relationManager
    ) {}

    public static function createModel(): ModelInterface
    {
        $class = static::getModelClass();
        return new $class();
    }

    public static function getModelProperties(): array
    {
        return static::createModel()->getProperties();
    }

    /**
     * @param array $attributes
     * @param array $conditions
     * @return ModelInterface
     */
    public function processData(array $attributes, array $conditions): ModelInterface
    {
        if (empty($conditions)) {
            throw new \InvalidArgumentException("{$this->getModelClass()}: Conditions cannot be empty");
        }

        $model = $this->findOne($conditions);

        if ($model) {
            if ($this->needUpdate($model, $attributes)) {
                return $this->update([
                    'id' => $model->getId(),
                    ...$attributes
                ]);
            }
            return $model;
        }

        return $this->create($attributes);
    }

    public function findOne(array $conditions): ModelInterface|false
    {
        $where = $this->queryBuilder->buildWhere($conditions, static::getModelClass());
        $stmt = $this->connection->prepare("SELECT * FROM {$this->getTableName()} {$where->sql} LIMIT 1");
        $stmt->execute($where->params);
        $stmt->setFetchMode($this->connection::FETCH_CLASS, $this->getModelClass());

        return $stmt->fetch();
    }

    public function create(array $attributes): ModelInterface
    {
        $insert = $this->queryBuilder->buildInsert($attributes, $this->getModelClass());
        if (!$this->connection
            ->prepare("INSERT INTO {$this->getTableName()} ( $insert->columns ) VALUES ( $insert->placeholders )")
            ->execute($attributes)
        ) {
            throw new CreateFailedException("Failed to create record in {$this->getTableName()}");
        }

        return $this->createModel()->setAttributes([
            'id' => (int)$this->connection->lastInsertId(),
            ...$attributes
        ]);
    }

    public function update(array $attributes): ModelInterface
    {
        if (!isset($attributes['id'])) {
            throw new \InvalidArgumentException("{$this->getModelClass()}: ID is required for update");
        }
        $this->queryBuilder->validateColumnNames(array_keys($attributes), $this->getModelClass());
        $set = $this->queryBuilder->buildUpdate($attributes, $this->getModelClass());
        if (!$this->connection
            ->prepare("UPDATE {$this->getTableName()} $set->sql WHERE id = :id")
            ->execute($attributes)
        ) {
            throw new UpdateFailedException("Failed to update record in {$this->getTableName()}");
        }

        return $this->createModel()->setAttributes($attributes);
    }

    // TODO: add cache
    public function countAll(array $queryParams = []): int
    {
        $where = $this->queryBuilder->buildFilter($queryParams, $this);
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM {$this->getTableName()} $where->sql ");
        $stmt->execute($where->params);
        return (int)$stmt->fetchColumn();
    }

    public function findFiltered(int $offset, int $limit, array $queryParams = []): array
    {
        $where = $this->queryBuilder->buildFilter($queryParams, $this);
        $join = $this->relationManager->buildJoin($this);
        $stmt = $this->connection->prepare("SELECT `{$this->getTableName()}`.* {$join->select}
            FROM {$this->getTableName()}
            {$join->sql}
            {$where->sql}
            LIMIT :limit OFFSET :offset");
        $stmt->execute([
            'offset' => $offset,
            'limit' => $limit,
            ...$where->params,
        ]);
        return $stmt->fetchAll();
    }

    private function needUpdate(ModelInterface $model, array $attributes): bool
    {
        foreach ($attributes as $propertyName => $value) {
            if ($model->getValue($propertyName) !== $value) {
                return true;
            }
        }
        return false;
    }
}
