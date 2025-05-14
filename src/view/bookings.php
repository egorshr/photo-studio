<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр записей</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            background: white;
            padding: 30px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .storage-info {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #5c67f2;
        }

        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            display: inline-block;
            margin: 5px;
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

        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Просмотр записей</h1>

    <div class="storage-info">
        Текущее хранилище: <?= isset($storageType) && $storageType === 'db' ? 'База данных' : 'CSV файл' ?>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="no-records">
            Записей не найдено
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <?php if (isset($storageType) && $storageType === 'db'): ?>
                    <th>ID</th>
                <?php endif; ?>
                <th>Имя</th>
                <th>Услуга</th>
                <th>Фотограф</th>
                <th>Дата</th>
                <?php if (isset($storageType) && $storageType === 'db'): ?>
                    <th>Создано</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <?php if (isset($storageType) && $storageType === 'db'): ?>
                        <td><?= htmlspecialchars($booking['id']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($booking['name']) ?></td>
                    <td><?= htmlspecialchars($booking['service']) ?></td>
                    <td><?= htmlspecialchars($booking['photographer']) ?></td>
                    <td><?= htmlspecialchars($booking['date']) ?></td>
                    <?php if (isset($storageType) && $storageType === 'db'): ?>
                        <td><?= htmlspecialchars($booking['created_at']) ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="actions">
        <a href="?route=form" class="button">Новая запись</a>
        <?php if (isset($storageType) && $storageType === 'csv'): ?>
            <a href="?route=migrate" class="button">Мигрировать данные в БД</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>