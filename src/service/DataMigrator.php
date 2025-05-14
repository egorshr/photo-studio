<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/Booking.php';

class DataMigrator
{
    private const CSV_FILE_PATH = __DIR__ . '/../data/bookings.csv';

    public static function migrateFromCsvToDb(): int
    {

        Database::initDatabase();
        $db = Database::getConnection();


        if (!file_exists(self::CSV_FILE_PATH)) {
            return 0;
        }

        $file = fopen(self::CSV_FILE_PATH, 'r');
        if (!$file) {
            throw new Exception('Не удалось открыть файл CSV');
        }


        fgetcsv($file);


        $stmt = $db->prepare("INSERT INTO bookings (name, service, photographer, date) VALUES (?, ?, ?, ?)");

        $migrated = 0;

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 4) {
                $stmt->execute([
                    $data[0], // name
                    $data[1], // service
                    $data[2], // photographer
                    $data[3], // date
                ]);
                $migrated++;
            }
        }

        fclose($file);


        $file = fopen(self::CSV_FILE_PATH, 'w');
        fputcsv($file, ['name', 'service', 'photographer', 'date']);
        fclose($file);

        return $migrated;
    }
}