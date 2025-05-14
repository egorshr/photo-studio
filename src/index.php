<?php

require_once __DIR__ . '/Router.php';

require_once __DIR__ . '/model/Database.php';
Database::initDatabase();

$router = new Router();
$router->handleRequest($_GET['route'] ?? '', $_SERVER['REQUEST_METHOD']);