<?php

use JetBrains\PhpStorm\NoReturn;

require_once __DIR__ . '/../model/Booking.php';
require_once __DIR__ . '/../model/Photographer.php';
require_once __DIR__ . '/../model/Service.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../repository/BookingRepository.php';
require_once __DIR__ . '/../service/DataMigrator.php';

class BookingController
{
    private BookingRepository $repository;

    public function __construct()
    {
        $this->repository = new BookingRepository();
        Database::initDatabase();
    }

    public function showForm(): void
    {
        $errors = [];
        $data = $_POST ?? [];
        $storageType = $_COOKIE['storage_type'] ?? 'csv';
        require __DIR__ . '/../view/form.php';
    }

    public function submitForm(): void
    {
        $errors = [];
        $data = $_POST ?? [];
        $service = null;
        $photographer = null;
        $storageType = $_COOKIE['storage_type'] ?? 'csv';

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


            $this->repository->saveBooking($booking, $storageType);

            header('Location: ?route=success');
            exit;
        }

        require __DIR__ . '/../view/form.php';
    }

    public function showSuccess(): void
    {
        require __DIR__ . '/../view/success.php';
    }

    public function migrateData(): void
    {
        try {
            $migratedCount = DataMigrator::migrateFromCsvToDb();
            $message = "Успешно мигрировано записей: $migratedCount";
        } catch (Exception $e) {
            $message = "Ошибка при миграции данных: " . $e->getMessage();
        }

        $storageType = $_COOKIE['storage_type'] ?? 'csv';
        require __DIR__ . '/../view/migrate.php';
    }

    #[NoReturn] public function setStorageType(): void
    {
        $type = $_POST['storage_type'] ?? 'csv';


        setcookie('storage_type', $type, time() + 30 * 24 * 60 * 60, '/');

        header('Location: ?route=form');
        exit;
    }

    public function showBookings(): void
    {
        $storageType = $_COOKIE['storage_type'] ?? 'csv';

        $filters = [
            'name' => $_GET['filter_name'] ?? '',
            'service' => $_GET['filter_service'] ?? '',
            'photographer' => $_GET['filter_photographer'] ?? '',
            'date_from' => $_GET['filter_date_from'] ?? '',
            'date_to' => $_GET['filter_date_to'] ?? ''
        ];

        if ($storageType === 'db') {
            $bookings = $this->repository->getAllBookingsFromDb($filters);
        } else {
            $bookings = $this->repository->getAllBookingsFromCsv($filters);
        }

        $availableServices = Service::getAvailableServices();
        $availablePhotographers = Photographer::getAvailablePhotographers();

        require __DIR__ . '/../view/bookings.php';
    }
}