<?php

namespace App\Model;

interface ModelInterface
{
    public function getId(): int;

    public function setAttributes(array $attributes): self;
}