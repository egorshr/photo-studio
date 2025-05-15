<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/Booking.php';

class DataMigrator
{
    private const CSV_FILE_PATTERN = __DIR__ . '/../data/bookings_%d.csv';

    public static function migrateFromCsvToDb(int $userId): int
    {
        Database::initDatabase();
        $db = Database::getConnection();

        $filePath = sprintf(self::CSV_FILE_PATTERN, $userId);
        if (!file_exists($filePath)) {
            return 0;
        }

        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new Exception('Не удалось открыть файл CSV');
        }

        fgetcsv($file);

        $stmt = $db->prepare("INSERT INTO bookings (name, service, photographer, date, user_id) VALUES (?, ?, ?, ?, ?)");

        $migrated = 0;

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 4) {
                $stmt->execute([
                    $data[0],                // name
                    $data[1],                // service
                    $data[2],                // photographer
                    $data[3],                // date
                    $data[4] ?? $userId      // user_id
                ]);
                $migrated++;
            }
        }

        fclose($file);


        $file = fopen($filePath, 'w');
        fputcsv($file, ['name', 'service', 'photographer', 'date', 'user_id']);
        fclose($file);

        return $migrated;
    }
}