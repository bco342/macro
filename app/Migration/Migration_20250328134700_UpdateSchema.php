<?php declare(strict_types=1);

namespace App\Migration;

/**
 * Для имени файла требуется следующий формат: Migration_<Timestamp>_DescriptiveName.php
 * Пример: Migration_20231001120000_CreateUsersTable.php
 */
class Migration_20250328134700_UpdateSchema extends Migration
{
    public function up(): bool
    {
        $this->connection->exec("
            ALTER TABLE estate
                ADD `agency_id` BIGINT UNSIGNED NOT NULL,
                ADD `external_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
                ADD INDEX `estate_agency_id_foreign` (`agency_id`),
                ADD INDEX `external_id` (`external_id`),
                ADD CONSTRAINT `estate_agency_id_foreign` FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`);"
            );
        $this->connection->exec("
            ALTER TABLE contacts
                ADD `agency_id` BIGINT UNSIGNED NOT NULL,
                ADD INDEX `contacts_agency_id_foreign` (`agency_id`),
                ADD CONSTRAINT `contacts_agency_id_foreign` FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`);"
        );
        return true;
    }

    public function down(): bool
    {
        $this->connection->exec("ALTER TABLE contacts DROP FOREIGN KEY `contacts_agency_id_foreign`");
        $this->connection->exec("ALTER TABLE contacts DROP COLUMN `agency_id`");
        $this->connection->exec("ALTER TABLE estate DROP FOREIGN KEY `estate_agency_id_foreign`");
        $this->connection->exec("ALTER TABLE estate DROP COLUMN `external_id`,  DROP COLUMN `agency_id`");
        return true;
    }
}
