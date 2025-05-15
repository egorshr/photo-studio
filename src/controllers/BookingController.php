<?php

use JetBrains\PhpStorm\NoReturn;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId <= 0) {
                $errors[] = "Необходимо авторизоваться для создания записи";
                require __DIR__ . '/../view/form.php';
                return;
            }

            $booking = new Booking(
                $name,
                $service->getName(),
                $photographer->getName(),
                $date,
                $userId
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
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId <= 0) {
                throw new Exception("Необходимо авторизоваться для миграции данных");
            }

            $migratedCount = DataMigrator::migrateFromCsvToDb($userId);
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
        $userId = $_SESSION['user_id'] ?? 0;

        if ($userId <= 0) {
            header('Location: ?route=login');
            exit;
        }

        $filters = [
            'name' => $_GET['filter_name'] ?? '',
            'service' => $_GET['filter_service'] ?? '',
            'photographer' => $_GET['filter_photographer'] ?? '',
            'date_from' => $_GET['filter_date_from'] ?? '',
            'date_to' => $_GET['filter_date_to'] ?? ''
        ];

        if ($storageType === 'db') {
            $bookings = $this->repository->getAllBookingsFromDb($filters, $userId);
        } else {
            $bookings = $this->repository->getAllBookingsFromCsv($filters, $userId);
        }

        $availableServices = Service::getAvailableServices();
        $availablePhotographers = Photographer::getAvailablePhotographers();

        require __DIR__ . '/../view/bookings.php';
    }
    #[NoReturn] public function generatePdfReport(): void
    {
        $bookings = $this->getFilteredBookings();

        $html = $this->renderPdfHtml($bookings);


        $tempDir = sys_get_temp_dir() . '/mpdf_tmp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }


        chmod($tempDir, 0777);

        $mpdf = new Mpdf([
            'tempDir' => $tempDir,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('bookings_report.pdf', 'D');
        exit;
    }

    #[NoReturn] public function generateExcelReport(): void
    {
        $bookings = $this->getFilteredBookings();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Имя')
            ->setCellValue('C1', 'Услуга')
            ->setCellValue('D1', 'Фотограф')
            ->setCellValue('E1', 'Дата');


        $row = 2;
        foreach ($bookings as $booking) {
            $sheet->setCellValue('A'.$row, $booking['id'] ?? '')
                ->setCellValue('B'.$row, $booking['name'])
                ->setCellValue('C'.$row, $booking['service'])
                ->setCellValue('D'.$row, $booking['photographer'])
                ->setCellValue('E'.$row, $booking['date']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="bookings_report.xlsx"');
        $writer->save('php://output');
        exit;
    }

    #[NoReturn] public function generateCsvReport(): void
    {
        $bookings = $this->getFilteredBookings();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Имя', 'Услуга', 'Фотограф', 'Дата']);

        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking['id'] ?? '',
                $booking['name'],
                $booking['service'],
                $booking['photographer'],
                $booking['date']
            ]);
        }
        fclose($output);
        exit;
    }

    private function getFilteredBookings(): array
    {
        $storageType = $_COOKIE['storage_type'] ?? 'csv';
        $userId = $_SESSION['user_id'] ?? 0;
        $filters = [
            'name' => $_GET['filter_name'] ?? '',
            'service' => $_GET['filter_service'] ?? '',
            'photographer' => $_GET['filter_photographer'] ?? '',
            'date_from' => $_GET['filter_date_from'] ?? '',
            'date_to' => $_GET['filter_date_to'] ?? ''
        ];

        return $storageType === 'db'
            ? $this->repository->getAllBookingsFromDb($filters, $userId)
            : $this->repository->getAllBookingsFromCsv($filters, $userId);
    }

    private function renderPdfHtml(array $bookings): string
    {
        ob_start();
        include __DIR__ . '/../view/pdf_template.php';
        return ob_get_clean();
    }

}