<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/Booking.php';

class BookingRepository
{

    private const CSV_FILE_PATH = __DIR__ . '/../data/bookings.csv';


    public function saveBooking(Booking $booking, string $storage = 'csv'): void
    {
        if ($storage === 'db') {
            $this->saveToDatabase($booking);
        } else {
            $this->saveToCsv($booking);
        }
    }


    private function saveToDatabase(Booking $booking): void
    {
        try {
            Database::initDatabase();
            $db = Database::getConnection();

            $stmt = $db->prepare("INSERT INTO bookings (name, service, photographer, date) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $booking->getName(),
                $booking->getService(),
                $booking->getPhotographer(),
                $booking->getDate()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Ошибка при сохранении в базу данных: " . $e->getMessage());
        }
    }


    private function saveToCsv(Booking $booking): void
    {
        $filePath = self::CSV_FILE_PATH;
        $isNewFile = !file_exists($filePath);
        $file = fopen($filePath, 'a');

        if ($isNewFile) {
            fputcsv($file, ['name', 'service', 'photographer', 'date']);
        }

        fputcsv($file, [
            $booking->getName(),
            $booking->getService(),
            $booking->getPhotographer(),
            $booking->getDate()
        ]);

        fclose($file);
    }


    public function getAllBookingsFromDb(): array
    {
        try {
            Database::initDatabase();
            $db = Database::getConnection();

            $stmt = $db->query("SELECT * FROM bookings ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Ошибка при получении данных из базы: " . $e->getMessage());
        }
    }


    public function getAllBookingsFromCsv(): array
    {
        $filePath = self::CSV_FILE_PATH;
        if (!file_exists($filePath)) {
            return [];
        }

        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new Exception('Не удалось открыть файл CSV');
        }


        fgetcsv($file);

        $bookings = [];
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 4) {
                $bookings[] = [
                    'name' => $data[0],
                    'service' => $data[1],
                    'photographer' => $data[2],
                    'date' => $data[3]
                ];
            }
        }

        fclose($file);
        return array_reverse($bookings);
    }
}