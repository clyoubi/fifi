<?php



header('Content-Type: application/json');


session_start();

$jsonObjectHeader = json_decode(file_get_contents('php://input'), true);

require_once 'config.php';
require_once 'Router.php';
require_once 'database/DB.php';
require_once 'database/Response.php';
require_once 'database/Auth.php';
require_once 'resources/lang/Lang.php';

//models
require_once 'models/abstractModel.php';
require_once 'models/Artist.php';


//controllers
require_once 'controllers/abstractController.php';
require_once 'controllers/UserController.php';

//Middlewares
require_once 'middleware/AuthMiddleware.php';


//functions
require_once 'functions.php';

$lang_code = locale_get_default();
$lang = require_once("resources/lang/$lang_code.php");
