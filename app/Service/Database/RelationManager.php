<?php declare(strict_types=1);

namespace App\Service\Database;

use App\Repository\HasRelationsInterface;
use App\Repository\TableMetadataInterface;
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
         * @var HasRelationsInterface $targetClass
         * @var ModelRelation $modelRelation
         */
        foreach ($repository->getRelations() as $targetClass => $modelRelation) {
            $this->validateJoin($targetClass, $modelRelation->joinType, $mainModelClass);

            $targetTable = $targetClass::getTableName();
            $onClauses = $this->buildOnClauses($mainTable, $targetTable, $modelRelation->conditions);

            $joins[] = "$modelRelation->joinType JOIN `$targetTable` ON " . implode(' AND ', $onClauses);
            $select = array_merge($select, $this->buildSelect($targetClass, $modelRelation->excludes));
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
    private function validateJoin(string $targetClass, string $joinType, string $mainModelClass): void
    {
        if (!is_a($targetClass, TableMetadataInterface::class, true)) {
            throw new \InvalidArgumentException("$targetClass must implement TableMetadataInterface");
        }

        if (!in_array($joinType, ['LEFT', 'INNER', 'RIGHT'])) {
            throw new \InvalidArgumentException("Invalid join type '$joinType' for $mainModelClass");
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

    private function buildSelect(string $targetClass, array $excludes): array
    {
        $select = [];
        foreach ($targetClass::getModelProperties() as $field) {
            if (!in_array($field, $excludes)) {
                $select[] = "`{$targetClass::getTableName()}`.`$field` AS {$targetClass::getTableName()}_$field";
            }
        }
        return $select;
    }

}