<?php



header('Content-Type: application/json');


session_start();

$jsonObjectHeader = json_decode(file_get_contents('php://input'), true);

require_once 'config.php';
require_once 'router.php';
require_once 'models/abstractModel.php';
require_once 'controllers/abstractController.php';
require_once 'middleware/AuthMiddleware.php';


function autoloadDirectory($dirPath)
{
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $dirPath;

    if (!is_dir($fullPath)) {
        return;
    }

    foreach (scandir($fullPath) as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $fullPath . DIRECTORY_SEPARATOR . $file;

        // Recursively load files in subdirectories too, if needed
        if (is_dir($filePath)) {
            autoloadDirectory($dirPath . DIRECTORY_SEPARATOR . $file);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
            require_once $filePath;
        }
    }
}


// List of folders to include
$folders = ['models', 'database', 'controllers', 'middleware'];

foreach ($folders as $folder) {
    autoloadDirectory($folder);
}

//functions
require_once 'functions.php';
