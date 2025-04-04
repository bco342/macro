<?php

namespace App\Service\Importer;

class DataMapper
{
    public function __construct(
        private array $mappings,
    ) {}

    public function mapData(array $row): array
    {
        $result = [];
        /**
         * @var string $column
         * @var ImportRule $rule
         */
        foreach ($this->mappings as $column => $rule) {
            if (!isset($row[$column])) {
                throw new \InvalidArgumentException("Column $column is required");
            }
            if (isset($rule->callback) && is_callable($rule->callback)) {
                $result[$rule->model][$rule->property] = call_user_func($rule->callback, $row[$column]);
            } else {
                $result[$rule->model][$rule->property] = $row[$column];
            }
        }
        return $result;
    }

    public function validateHeaders(array $headers): bool
    {
        $missing = array_diff(array_keys($this->mappings), $headers);

        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                "Missing required columns: " . implode(', ', $missing)
            );
        }

        return true;
    }

}