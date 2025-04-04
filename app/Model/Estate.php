<?php declare(strict_types=1);

namespace App\Model;

class Estate extends Model
{
    protected int $id;
    protected string $address;
    protected int $price;
    protected int $rooms;
    protected int $floor;
    protected int $house_floors;
    protected string $description;
    protected int $contact_id;
    protected int $manager_id;
    protected int $agency_id;
    protected string $external_id;
}
