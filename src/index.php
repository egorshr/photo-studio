<?php
require_once __DIR__ . '/Router.php';

$router = new Router();
$router->handleRequest($_GET['route'] ?? '', $_SERVER['REQUEST_METHOD']);
