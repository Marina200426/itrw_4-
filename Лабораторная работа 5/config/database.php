<?php

/**
 * Конфигурация подключения к базе данных SQLite
 */
class DatabaseConfig
{
    private string $dbPath;

    public function __construct(string $dbPath = null)
    {
        $this->dbPath = $dbPath ?? __DIR__ . '/../database/app.db';
    }

    public function getConnection(): PDO
    {
        $pdo = new PDO('sqlite:' . $this->dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }

    public function initializeDatabase(): void
    {
        $schemaPath = __DIR__ . '/../database/schema.sql';
        if (!file_exists($schemaPath)) {
            throw new Exception("Schema file not found: {$schemaPath}");
        }

        $pdo = $this->getConnection();
        $sql = file_get_contents($schemaPath);
        $pdo->exec($sql);
    }
}

