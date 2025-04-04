<?php declare(strict_types=1);

namespace App\Model;

class Contact extends Model
{
    protected int $id;
    protected string $name;
    protected string $phones;
    protected int $agency_id;
}
