<?php declare(strict_types=1);

namespace App\Model;

abstract class Model implements ModelInterface
{
    public function getId(): int
    {
        return $this->id;
    }

    public function setAttributes(array $attributes): ModelInterface
    {
        foreach ($attributes as $property => $value) {
            if (!property_exists($this, $property)) {
                continue;
            }
            $this->$property = $value;
        }
        return $this;
    }

    public function getValue(string $propertyName): mixed
    {
        if (!property_exists($this, $propertyName)) {
            throw new \RuntimeException(__CLASS__ . ': Wrong property name: ' . $propertyName);
        }
        return $this->$propertyName;
    }

    public function getProperties(): array
    {
        return array_keys(get_class_vars(get_class($this)));
    }
}