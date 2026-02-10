<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {--connection= : The database connection to use}';

    protected $description = 'Create the database on PostgreSQL or MySQL server';

    public function handle(): int
    {
        $connectionName = $this->option('connection') ?? config('database.default');
        $config = config("database.connections.{$connectionName}");

        if (! $config) {
            $this->error("Connection [{$connectionName}] not found in database config.");

            return self::FAILURE;
        }

        $driver = $config['driver'];
        $database = $config['database'];
        $host = $config['host'];
        $port = $config['port'];
        $username = $config['username'];
        $password = $config['password'];

        if (! in_array($driver, ['pgsql', 'mysql', 'mariadb'])) {
            $this->error("Driver [{$driver}] is not supported. Only pgsql, mysql, and mariadb are supported.");

            return self::FAILURE;
        }

        $this->info("Creating database [{$database}] on {$driver}://{$host}:{$port}...");

        try {
            if ($driver === 'pgsql') {
                $this->createPostgresDatabase($host, $port, $username, $password, $database);
            } else {
                $this->createMysqlDatabase($host, $port, $username, $password, $database, $config);
            }

            $this->info("Database [{$database}] created successfully!");

            return self::SUCCESS;
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->warn("Database [{$database}] already exists.");

                return self::SUCCESS;
            }

            $this->error("Failed to create database: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    protected function createPostgresDatabase(string $host, string $port, string $username, string $password, string $database): void
    {
        $dsn = "pgsql:host={$host};port={$port};dbname=postgres";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if database exists
        $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
        $stmt->execute([$database]);

        if ($stmt->fetch()) {
            throw new PDOException("Database \"{$database}\" already exists.");
        }

        // PostgreSQL doesn't support prepared statements for CREATE DATABASE
        $pdo->exec("CREATE DATABASE \"{$database}\"");
    }

    protected function createMysqlDatabase(string $host, string $port, string $username, string $password, string $database, array $config): void
    {
        $dsn = "mysql:host={$host};port={$port}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $charset = $config['charset'] ?? 'utf8mb4';
        $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}");
    }
}
