#!/usr/bin/env php
<?php

// Self-contained CLI for "fifi" framework

main($argv);

function main($argv)
{
    $command = $argv[1] ?? null;

    try {
        loadEnv(__DIR__ . '/.env');

        switch ($command) {
            case 'migrate':
                migrateDatabase();
                break;
            default:
                echo "❓ Unknown or missing command. Available commands:\n";
                echo "   php cli.php migrate\n";
                break;
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function loadEnv(string $file)
{
    if (!file_exists($file)) {
        throw new Exception(".env file not found at $file");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;

        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

function migrateDatabase()
{
    $host = getenv('DATABASE_HOST');
    $dbname = getenv('DATABASE_NAME');
    $user = getenv('DATABASE_USER');
    $pass = getenv('DATABASE_PASSWORD');

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    $sqlFile = __DIR__ . '/database/db.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found at $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    if (!$sql) {
        throw new Exception("Could not read SQL file.");
    }

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "🚀 Running migration (idempotency test)...\n";

    for ($i = 1; $i <= 2; $i++) {
        echo "  Pass $i...\n";
        $pdo->exec($sql);
    }

    echo "✅ Migration complete and idempotent.\n";
}
