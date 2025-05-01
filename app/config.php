<?php
header('Content-Type: application/json');

function loadEnvAndDefineConstants(string $path = __DIR__ . '/.env') {
    if (!file_exists($path)) {
        throw new Exception(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        putenv("$key=$value"); // optional
        if (!defined($key)) {
            define($key, $value);
        }
    }
}


define('APP_NAME', 'App Name');
define('APP_DESCRIPTION', 'App Description');
define('APP_ICON', __DIR__."/assets/images/icon.jpg");
define('APP_TAG', '');
define('BASE_PATH', __DIR__);
define('SECRET_KEY', "7zPBXz1YCdxK634BtUVnS1MwvrQK0tYZZ21QWptPoQ=");