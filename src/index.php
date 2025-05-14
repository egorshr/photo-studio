<?php

require_once __DIR__ . '/Router.php';

// Инициализация базы данных (если используется)
require_once __DIR__ . '/model/Database.php';
Database::initDatabase();

// Обработка запроса
$router = new Router();
$router->handleRequest($_GET['route'] ?? '', $_SERVER['REQUEST_METHOD']);