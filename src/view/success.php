<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Успешно</title>
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

        .success-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .nav-links {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button {
            display: inline-block;
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
    </style>
</head>
<body>
<div class="container">
    <div class="success-icon">✓</div>
    <h1>Запись успешно отправлена!</h1>

    <div class="nav-links">
        <a href="?route=form" class="button">Новая запись</a>
        <a href="?route=bookings" class="button">Все записи</a>
    </div>
</div>
</body>
</html>