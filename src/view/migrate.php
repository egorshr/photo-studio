<?php

$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$userRole = $_SESSION['role'] ?? '';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Миграция данных</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #5c67f2;
            font-weight: bold;
        }

        a:hover {
            color: #333;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #5c67f2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .button:hover {
            background-color: #434de2;
        }

        .user-info {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: right;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .user-info span {
            margin-right: 15px;
        }

        .user-info a {
            color: #5c67f2;
            text-decoration: none;
            margin-left: 10px;
        }

        .user-info a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Миграция данных из CSV в базу данных</h1>
    <div class="user-info">
        <?php if ($isLoggedIn): ?>
            <span>Пользователь: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($userRole) ?>)</span>
            <a href="?route=logout">Выйти</a>
        <?php else: ?>
            <a href="?route=login">Войти</a>
            <a href="?route=register">Регистрация</a>
        <?php endif; ?>
    </div>
    <div class="message <?= isset($message) && str_contains($message, 'Ошибка') ? 'error' : '' ?>">
        <?= htmlspecialchars($message ?? 'Операция выполнена') ?>
    </div>

    <a href="?route=form" class="button">Вернуться к форме</a>
    <a href="?route=bookings" class="button">Просмотр записей</a>
</div>
</body>
</html>