<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/model/Database.php';


session_start();


Database::initDatabase();

$router = new Router();
$router->handleRequest($_GET['route'] ?? '', $_SERVER['REQUEST_METHOD']);