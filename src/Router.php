<?php

require_once __DIR__ . '/controllers/BookingController.php';
require_once __DIR__ . '/controllers/AuthController.php';


class Router
{
    public function handleRequest(string $route, string $method): void
    {
        $bookingController = new BookingController();
        $authController = new AuthController();

        switch ($route) {
            case '':
            case 'form':

                AuthController::requireLogin();

                if ($method === 'POST') {
                    $bookingController->submitForm();
                } else {
                    $bookingController->showForm();
                }
                break;

            case 'success':
                AuthController::requireLogin();
                $bookingController->showSuccess();
                break;

            case 'migrate':
                AuthController::requireLogin();
                $bookingController->migrateData();
                break;

            case 'set-storage':
                AuthController::requireLogin();
                $bookingController->setStorageType();
                break; 

            case 'bookings':
                AuthController::requireLogin();
                $bookingController->showBookings();
                break;


            case 'login':
                if ($method === 'POST') {
                    $authController->login();
                } else {
                    $authController->showLoginForm();
                }
                break;

            case 'register':
                if ($method === 'POST') {
                    $authController->register();
                } else {
                    $authController->showRegisterForm();
                }
                break;

            case 'logout':
                $authController->logout();
                break;

            case 'generate-pdf':
                AuthController::requireLogin();
                $bookingController->generatePdfReport();
                break;

            case 'generate-excel':
                AuthController::requireLogin();
                $bookingController->generateExcelReport();
                break;

            case 'generate-csv':
                AuthController::requireLogin();
                $bookingController->generateCsvReport();
                break;

            default:
                http_response_code(404);
                echo "Страница не найдена";
                break;
        }
    }
}