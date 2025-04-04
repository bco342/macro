<?php declare(strict_types=1);

namespace App\Service\Database;

use App\Repository\FilterableInterface;
use App\Service\Database\Query\InsertClause;
use App\Service\Database\Query\SetClause;
use App\Service\Database\Query\WhereClause;

class QueryBuilder
{
    /**
     * @param array $conditions
     * @param string $modelClass
     * @return WhereClause
     * @throws \InvalidArgumentException
     */
    public function buildWhere(array $conditions, string $modelClass): WhereClause
    {
        $whereParts = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            $this->validateColumnName($field, $modelClass);
            $whereParts[] = "$field = :$field";
            $params[$field] = $value;
        }

        return new WhereClause(
            sql: $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '',
            params: $params
        );
    }

    /**
     * @param array $attributes
     * @param string $modelClass
     * @return SetClause
     * @throws \InvalidArgumentException
     */
    public function buildUpdate(array $attributes, string $modelClass): SetClause
    {
        $setParts = [];
        unset($attributes['id']);
        foreach ($attributes as $field => $value) {
            $this->validateColumnName($field, $modelClass);
            $setParts[] = "$field = :$field";
        }

        return new SetClause(
            sql: ' SET ' . implode(', ', $setParts)
        );
    }

    public function buildFilter(array $queryParams, FilterableInterface $repository): WhereClause
    {
        $tableName = $repository->getTableName();
        $whereParts = [];
        $params = [];

        foreach ($queryParams as $queryParam => $value) {
            if (!empty($value)) {
                $field = $repository->mapFilterToProperty($queryParam);
                if (!empty($field)) {
                    $whereParts[] = "$tableName.$field = :$queryParam";
                    $params[$queryParam] = $value;
                }
            }
        }

        return new WhereClause(
            sql: $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '',
            params: $params
        );
    }

    /**
     * @param array $attributes
     * @param string $modelClass
     * @return InsertClause
     * @throws \InvalidArgumentException
     */
    public function buildInsert(array $attributes, string $modelClass): InsertClause
    {
        $columnNames = array_keys($attributes);
        $this->validateColumnNames($columnNames, $modelClass);

        return new InsertClause(
            columns: implode(',', $columnNames),
            placeholders: ':' . implode(',:', $columnNames)
        );
    }

    /**
     * @param array $columnNames
     * @throws \InvalidArgumentException
     */
    public function validateColumnNames(array $columnNames, string $modelClass): void
    {
        foreach ($columnNames as $columnName) {
            $this->validateColumnName($columnName, $modelClass);
        }
    }

    /**
     * @param array $columnNames
     * @throws \InvalidArgumentException
     */
    public function validateColumnName(string $columnName, string $modelClass): void
    {
        if (!property_exists($modelClass, $columnName)) {
            throw new \InvalidArgumentException("Invalid column: $columnName");
        }
    }
}