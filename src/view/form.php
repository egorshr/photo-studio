<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Запись в фотостудию</title>
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
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #5c67f2;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #434de2;
        }

        .errors {
            background-color: #ffe0e0;
            border: 1px solid #cc0000;
            padding: 10px;
            margin-bottom: 20px;
            color: #cc0000;
            border-radius: 4px;
        }

        .storage-selector {
            background-color: #f2f2f2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .storage-selector h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }

        .storage-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .storage-form label {
            margin-bottom: 0;
            margin-right: 10px;
            display: inline-flex;
            align-items: center;
        }

        .storage-form label input {
            width: auto;
            margin-right: 5px;
            margin-bottom: 0;
        }

        .storage-form button {
            width: auto;
            padding: 8px 15px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-links a {
            color: #5c67f2;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Запись в фотостудию</h1>

    <div class="storage-selector">
        <h3>Выберите тип хранилища данных</h3>
        <form action="?route=set-storage" method="POST" class="storage-form">
            <div>
                <label>
                    <input type="radio" name="storage_type" value="csv" <?= (!isset($storageType) || $storageType === 'csv') ? 'checked' : '' ?>>
                    CSV файл
                </label>
                <label>
                    <input type="radio" name="storage_type" value="db" <?= (isset($storageType) && $storageType === 'db') ? 'checked' : '' ?>>
                    База данных
                </label>
            </div>
            <button type="submit">Применить</button>
        </form>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="POST" autocomplete="off">
        <label for="name">Имя</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">

        <label for="service">Услуга</label>
        <select name="service" id="service" required>
            <option value="">Выберите услугу</option>
            <?php foreach (Service::getAvailableServices() as $availableService): ?>
                <option value="<?= htmlspecialchars($availableService) ?>" <?= ($data['service'] ?? '') === $availableService ? 'selected' : '' ?>>
                    <?= htmlspecialchars($availableService) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="photographer">Фотограф</label>
        <select name="photographer" id="photographer" required>
            <option value="">Выберите фотографа</option>
            <?php foreach (Photographer::getAvailablePhotographers() as $availablePhotographer): ?>
                <option value="<?= htmlspecialchars($availablePhotographer) ?>" <?= ($data['photographer'] ?? '') === $availablePhotographer ? 'selected' : '' ?>>
                    <?= htmlspecialchars($availablePhotographer) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="date">Дата</label>
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($data['date'] ?? '') ?>">

        <button type="submit">Записаться</button>
    </form>

    <div class="nav-links">
        <a href="?route=bookings">Просмотр записей</a>
        <?php if (!isset($storageType) || $storageType === 'csv'): ?>
            <a href="?route=migrate">Мигрировать данные в БД</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>