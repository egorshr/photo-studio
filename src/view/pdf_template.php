<?php

$storageType = $storageType ?? $_COOKIE['storage_type'] ?? 'csv';
$bookings = $bookings ?? [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчет по бронированиям</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Отчет по бронированиям</h1>
<table>
    <tr>
        <?php if ($storageType === 'db'): ?><th>ID</th><?php endif; ?>
        <th>Имя</th>
        <th>Услуга</th>
        <th>Фотограф</th>
        <th>Дата</th>
    </tr>
    <?php foreach ($bookings as $booking): ?>
        <tr>
            <?php if ($storageType === 'db'): ?><td><?= $booking['id'] ?></td><?php endif; ?>
            <td><?= $booking['name'] ?></td>
            <td><?= $booking['service'] ?></td>
            <td><?= $booking['photographer'] ?></td>
            <td><?= $booking['date'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>