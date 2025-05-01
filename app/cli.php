#!/usr/bin/env php
<?php

// Self-contained CLI for "fifi" framework
require 'autoloader.php';

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


function migrateDatabase(string $modelsDir = __DIR__ . '/models')
{
    $db = DB::getInstance();

    foreach (glob($modelsDir . '/*.php') as $file) {
        require_once $file;

        $className = pathinfo($file, PATHINFO_FILENAME);

        if (!class_exists($className)) continue;

        $reflection = new ReflectionClass($className);
        if ($reflection->isSubclassOf(Model::class) && !$reflection->isAbstract()) {
            if ($reflection->hasMethod('generateSchema')) {
                echo "Migrating: $className\n";
                $schemaSQL = $className::generateSchema();
                $db->query($schemaSQL);
            }
        }
    }
}
