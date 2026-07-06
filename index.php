<?php
/**
 * GrandInap - Front Controller (satu-satunya pintu masuk aplikasi)
 * Pola MVC PHP Native + PDO MySQL
 */
session_start();

require_once __DIR__ . '/config/config.php';

define('APPROOT', __DIR__ . '/app');
define('ROOTPATH', __DIR__);

// Autoload sederhana untuk file core
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/helpers.php';
require_once APPROOT . '/core/Controller.php';
require_once APPROOT . '/core/Router.php';

$router = new Router();
$router->dispatch();
