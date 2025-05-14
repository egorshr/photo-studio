<?php

$storageType = $storageType ?? $_COOKIE['storage_type'] ?? 'csv';
$filters = $filters ?? [];
$availableServices = $availableServices ?? Service::getAvailableServices();
$availablePhotographers = $availablePhotographers ?? Photographer::getAvailablePhotographers();
$bookings = $bookings ?? [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр записей</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 960px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #222;
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .navigation a {
            text-decoration: none;
            color: #6a4c93;
            margin-right: 16px;
            font-weight: 500;
        }

        .navigation a:last-child {
            margin-right: 0;
        }

        .storage-info {
            font-size: 14px;
            color: #666;
        }

        .filter-panel {
            background: #fafafa;
            border: 1px solid #e0e0e5;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-panel h3 {
            margin-top: 0;
            font-size: 20px;
            color: #444;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .filter-group label {
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }

        .filter-group input[type="text"],
        .filter-group input[type="date"],
        .filter-group select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .apply-button,
        .reset-button,
        .button {
            background-color: #6a4c93;
            color: #fff;
            text-decoration: none;
            border: none;
            padding: 10px 18px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .apply-button:hover,
        .button:hover {
            background-color: #593b7a;
        }

        .reset-button {
            background-color: transparent;
            color: #6a4c93;
            padding: 10px 18px;
        }

        .reset-button:hover {
            color: #593b7a;
            background-color: rgba(105, 73, 146, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead th {
            text-align: left;
            padding: 12px;
            background: #f0f0f5;
            font-weight: 600;
            font-size: 15px;
            border-bottom: 2px solid #e0e0e5;
        }

        tbody td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e5;
            font-size: 14px;
        }

        .no-records {
            text-align: center;
            color: #777;
            padding: 30px 0;
            font-size: 16px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Просмотр записей</h1>
    <div class="navigation">
        <div>
            <a href="?route=form">Форма записи</a>
            <a href="?route=bookings">Все записи</a>
            <?php if ($storageType === 'csv'): ?>
                <a href="?route=migrate">Миграция данных</a>
            <?php endif; ?>
        </div>
        <div class="storage-info">
            Хранилище: <?= $storageType === 'db' ? 'База данных' : 'CSV файл' ?>
        </div>
    </div>

    <div class="filter-panel">
        <h3>Фильтры</h3>
        <form method="GET" action="?route=bookings">
            <input type="hidden" name="route" value="bookings">
            <div class="filter-group">
                <label for="filter_name">Имя</label>
                <input type="text" id="filter_name" name="filter_name"
                       value="<?= htmlspecialchars($filters['name'] ?? '') ?>">
            </div>
            <div class="filter-group">
                <label for="filter_service">Услуга</label>
                <select id="filter_service" name="filter_service">
                    <option value="">Все услуги</option>
                    <?php foreach ($availableServices as $service): ?>
                        <option value="<?= htmlspecialchars($service) ?>"
                            <?= ($filters['service'] ?? '') === $service ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filter_photographer">Фотограф</label>
                <select id="filter_photographer" name="filter_photographer">
                    <option value="">Все фотографы</option>
                    <?php foreach ($availablePhotographers as $photographer): ?>
                        <option value="<?= htmlspecialchars($photographer) ?>"
                            <?= ($filters['photographer'] ?? '') === $photographer ? 'selected' : '' ?>>
                            <?= htmlspecialchars($photographer) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="max-width: 200px;">
                <label for="filter_date_from">Дата от</label>
                <input type="date" id="filter_date_from" name="filter_date_from"
                       value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            </div>
            <div class="filter-group" style="max-width: 200px;">
                <label for="filter_date_to">Дата до</label>
                <input type="date" id="filter_date_to" name="filter_date_to"
                       value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
            </div>
            <div class="filter-buttons">
                <button type="submit" class="apply-button">Применить фильтры</button>
                <a href="?route=bookings" class="reset-button">Сбросить</a>
            </div>
        </form>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="no-records">
            <?php if (!empty(array_filter($filters))): ?>
                По заданным критериям записей не найдено
            <?php else: ?>
                Записей не найдено
            <?php endif; ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <?php if ($storageType === 'db'): ?>
                    <th>ID</th><?php endif; ?>
                <th>Имя</th>
                <th>Услуга</th>
                <th>Фотограф</th>
                <th>Дата</th>
                <?php if ($storageType === 'db'): ?>
                    <th>Дата создания</th><?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <?php if ($storageType === 'db'): ?>
                        <td><?= htmlspecialchars($booking['id'] ?? '') ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($booking['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['service'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['photographer'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['date'] ?? '') ?></td>
                    <?php if ($storageType === 'db'): ?>
                        <td><?= htmlspecialchars($booking['created_at'] ?? '') ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="actions">
        <a href="?route=form" class="button">Новая запись</a>
        <?php if ($storageType === 'csv'): ?>
            <a href="?route=migrate" class="button">Мигрировать в БД</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
