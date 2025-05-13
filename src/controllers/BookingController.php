<?php

require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Photographer.php';
require_once __DIR__ . '/../models/Service.php';

class BookingController
{

    public function showForm(): void
    {
        $errors = [];
        require __DIR__ . '/../views/form.php';
    }

    public function submitForm(): void
    {
        $errors = [];
        $data = $_POST ?? [];
        $service = null;
        $photographer = null;

        $name = trim($data['name'] ?? '');
        if (empty($name)) {
            $errors[] = "Имя не может быть пустым.";
        } elseif (mb_strlen($name) < 2) {
            $errors[] = "Имя должно содержать минимум 2 символа.";
        } elseif (!preg_match('/^[А-яЁёA-Za-z\s\-]+$/u', $name)) {
            $errors[] = "Имя может содержать только буквы, пробелы и дефисы.";
        }


        $date = $data['date'] ?? '';
        if (empty($date)) {
            $errors[] = "Дата не может быть пустой.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors[] = "Неверный формат даты.";
        } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
            $errors[] = "Дата не может быть в прошлом.";
        }


        try {
            $service = new Service($data['service'] ?? '');
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        try {
            $photographer = new Photographer($data['photographer'] ?? '');
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            $booking = new Booking(
                $name,
                $service->getName(),
                $photographer->getName(),
                $date
            );
            $this->saveBookingToCSV($booking);
            header('Location: ?route=success');
            exit;
        }

        require __DIR__ . '/../views/form.php';
    }

    public function showSuccess(): void
    {
        require __DIR__ . '/../views/success.php';
    }


    private function saveBookingToCSV(Booking $booking): void
    {
        $filePath = __DIR__ . '/../data/bookings.csv';
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
}