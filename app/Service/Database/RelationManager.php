<?php declare(strict_types=1);

namespace App\Service\Database;

use App\Repository\HasRelationsInterface;
use App\Service\Database\Query\JoinClause;
use App\Service\Database\Query\ModelRelation;

class RelationManager
{
    /**
     * @param array $relations
     * @param string $mainTable
     * @param string $mainModelClass
     * @return JoinClause
     * @throws \InvalidArgumentException
     */
    public function buildJoin(HasRelationsInterface $repository): JoinClause
    {
        $mainTable = $repository->getTableName();
        $mainModelClass = $repository->getModelClass();
        $select = [];
        $joins = [];
        /**
         * @var ModelRelation $relation
         */
        foreach ($repository->getRelations() as $relation) {
            $this->validateJoin($relation, $mainModelClass);

            $onClauses = $this->buildOnClauses($mainTable, $relation->targetTable, $relation->conditions);

            $joins[] = "$relation->joinType JOIN `$relation->targetTable` ON " . implode(' AND ', $onClauses);
            $select = array_merge($select, $this->buildSelect($relation));
        }

        return new JoinClause(
            select: $select ? ', ' . implode(', ', $select) : '',
            sql: implode(' ', $joins)
        );
    }

    /**
     * @param string $targetClass
     * @param string $joinType
     * @param string $mainModelClass
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateJoin(ModelRelation $relation, string $mainModelClass): void
    {
        if (!in_array($relation->joinType, $relation->getTypes())) {
            throw new \InvalidArgumentException("Invalid join type '$relation->joinType' for $mainModelClass");
        }
    }

    private function buildOnClauses(string $mainTable, string $targetTable, array $conditions): array
    {
        $onClauses = [];

        foreach ($conditions as $foreignKey => $primaryKey) {
            $onClauses[] = "`{$mainTable}`.`{$foreignKey}` = `{$targetTable}`.`{$primaryKey}`";
        }

        return $onClauses;
    }

    private function buildSelect(ModelRelation $relation): array
    {
        $select = [];
        foreach ($relation->targetColumnsToSelect as $field) {
            $select[] = "`$relation->targetTable`.`$field` AS {$relation->targetTable}_$field";
        }
        return $select;
    }

}