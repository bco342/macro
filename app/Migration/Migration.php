<?php declare(strict_types=1);

namespace App\Migration;

use PDO;

abstract class Migration implements MigrationInterface
{
    public function __construct(
        protected PDO $connection
    ) {}
    abstract public function up(): bool;
    abstract public function down(): bool;

}