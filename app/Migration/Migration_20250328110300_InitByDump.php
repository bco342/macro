<?php declare(strict_types=1);

namespace App\Migration;

/**
 * Для имени файла требуется следующий формат: Migration_<Timestamp>_DescriptiveName.php
 * Пример: Migration_20231001120000_CreateUsersTable.php
 */
class Migration_20250328110300_InitByDump extends Migration {

    public function up(): bool {
        $sql = file_get_contents(__DIR__ . '/../../data/dump.sql');
        $this->connection->exec($sql);
        return true;
    }

    public function down(): bool {
        $this->connection->exec("
            ALTER TABLE estate
                DROP FOREIGN KEY `estate_contact_id_foreign`,
                DROP FOREIGN KEY `estate_manager_id_foreign`;
            ALTER TABLE manager
                DROP FOREIGN KEY `manager_agency_id_foreign`;
            DROP TABLE agency;
            DROP TABLE manager;
            DROP TABLE contacts;
            DROP TABLE estate;
        ");
        return true;
    }
}
