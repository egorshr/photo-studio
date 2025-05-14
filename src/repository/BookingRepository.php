<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/Booking.php';

class BookingRepository
{
    private const CSV_FILE_PATH = __DIR__ . '/../data/bookings_%d.csv'; // Изменили путь для хранения по userId

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

            $stmt = $db->prepare("INSERT INTO bookings (name, service, photographer, date, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $booking->getName(),
                $booking->getService(),
                $booking->getPhotographer(),
                $booking->getDate(),
                $booking->getUserId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Ошибка при сохранении в базу данных: " . $e->getMessage());
        }
    }

    private function saveToCsv(Booking $booking): void
    {
        $filePath = sprintf(self::CSV_FILE_PATH, $booking->getUserId());
        $isNewFile = !file_exists($filePath);

        // Создаем директорию, если не существует
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = fopen($filePath, 'a');

        if ($isNewFile) {
            fputcsv($file, ['name', 'service', 'photographer', 'date', 'user_id']);
        }

        fputcsv($file, [
            $booking->getName(),
            $booking->getService(),
            $booking->getPhotographer(),
            $booking->getDate(),
            $booking->getUserId()
        ]);

        fclose($file);
    }

    public function getAllBookingsFromDb(array $filters = [], ?int $userId = null): array
    {
        try {
            Database::initDatabase();
            $db = Database::getConnection();

            $sql = "SELECT * FROM bookings WHERE 1=1";
            $params = [];

            // Добавляем фильтр по user_id, если указан
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }

            if (!empty($filters['name'])) {
                $sql .= " AND name LIKE ?";
                $params[] = '%' . $filters['name'] . '%';
            }

            if (!empty($filters['service'])) {
                $sql .= " AND service = ?";
                $params[] = $filters['service'];
            }

            if (!empty($filters['photographer'])) {
                $sql .= " AND photographer = ?";
                $params[] = $filters['photographer'];
            }

            if (!empty($filters['date_from'])) {
                $sql .= " AND date >= ?";
                $params[] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $sql .= " AND date <= ?";
                $params[] = $filters['date_to'];
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Ошибка при получении данных из базы: " . $e->getMessage());
        }
    }

    public function getAllBookingsFromCsv(array $filters = [], ?int $userId = null): array
    {
        if ($userId === null) {
            return [];
        }

        $filePath = sprintf(self::CSV_FILE_PATH, $userId);
        if (!file_exists($filePath)) {
            return [];
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Пропускаем заголовок

        $bookings = [];
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) >= 4) {
                $booking = [
                    'name' => $data[0],
                    'service' => $data[1],
                    'photographer' => $data[2],
                    'date' => $data[3],
                    'user_id' => $data[4] ?? $userId
                ];

                $match = true;

                if (!empty($filters['name']) &&
                    stripos($booking['name'], $filters['name']) === false) {
                    $match = false;
                }

                if (!empty($filters['service']) &&
                    $booking['service'] !== $filters['service']) {
                    $match = false;
                }

                if (!empty($filters['photographer']) &&
                    $booking['photographer'] !== $filters['photographer']) {
                    $match = false;
                }

                if (!empty($filters['date_from']) &&
                    $booking['date'] < $filters['date_from']) {
                    $match = false;
                }

                if (!empty($filters['date_to']) &&
                    $booking['date'] > $filters['date_to']) {
                    $match = false;
                }

                if ($match) {
                    $bookings[] = $booking;
                }
            }
        }

        fclose($file);
        return array_reverse($bookings);
    }
}