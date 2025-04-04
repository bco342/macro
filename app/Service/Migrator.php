<?php declare(strict_types=1);

namespace App\Service;

use App\Migration\MigrationInterface;
use PDO;
use Psr\Log\LoggerInterface;

class Migrator
{
    private string $tableName = 'migrations';

    public function __construct(
        private PDO             $connection,
        private string          $migrationsDir,
        private LoggerInterface $logger
    )
    {
        $this->ensureMigrationsTableExists();
    }

    private function ensureMigrationsTableExists(): void
    {
        $this->connection->exec("CREATE TABLE IF NOT EXISTS {$this->tableName} (id INT AUTO_INCREMENT PRIMARY KEY, migration_name VARCHAR(255) UNIQUE NOT NULL, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    }

    /**
     * Применяем все новые миграции
     * @return void
     * @throws \Throwable
     */
    public function migrateUp(): void
    {
        $applied = $this->getAppliedMigrations();
        $files = $this->getMigrationFiles();

        foreach ($files as $file) {
            if (!in_array($file, $applied)) {
                $this->applyMigration($file);
            }
        }
    }

    /**
     * Откатываем только последнюю миграцию
     * @return void
     * @throws \Throwable
     */
    public function migrateDown(): void
    {
        $applied = $this->getAppliedMigrations();
        if (empty($applied)) {
            return;
        }
        $lastMigration = end($applied);
        $this->revertMigration($lastMigration);
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->connection->query("SELECT migration_name FROM {$this->tableName}");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getMigrationFiles(): array
    {
        $files = scandir($this->migrationsDir);
        return array_filter($files, fn($f) => preg_match('~^Migration_\d{14}_[A-Za-z0-9]+\.php$~', $f));
    }

    private function applyMigration(string $filename): void
    {
        $this->connection->beginTransaction();
        try {
            $migration = $this->loadMigration($filename);
            if (!$migration->up()) {
                throw new \RuntimeException('Migration failed');
            }

            $stmt = $this->connection->prepare("INSERT INTO {$this->tableName} (migration_name) VALUES (?)");
            $stmt->execute([$filename]);

            if ($this->connection->inTransaction()) {
                $this->connection->commit();
            }
        } catch (\Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            $this->logger->error("Migration failed: " . $e->getMessage(), [
                'file' => $filename,
                'trace' => $e->getTrace()
            ]);
            throw $e;
        }
    }

    private function revertMigration(string $filename): void
    {
        $this->connection->beginTransaction();
        try {
            $migration = $this->loadMigration($filename);
            if (!$migration->down()) {
                throw new \RuntimeException('Migration failed');
            }

            $stmt = $this->connection->prepare("DELETE FROM {$this->tableName} WHERE migration_name = ?");
            $stmt->execute([$filename]);

            if ($this->connection->inTransaction()) {
                $this->connection->commit();
            }
        } catch (\Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }
    private function loadMigration(string $filename): MigrationInterface
    {
        $className = $this->filenameToClassName($filename);

        if (!class_exists($className)) {
            throw new \RuntimeException("Migration class {$className} not found");
        }

        return new $className($this->connection);
    }

    private function filenameToClassName(string $filename): string
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        return "App\\Migration\\{$baseName}";
    }
}
