<?php

namespace App\Migration;

interface MigrationInterface
{
    public function up(): bool;
    public function down(): bool;
}