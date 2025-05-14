<?php

require_once __DIR__ . '/controllers/BookingController.php';

class Router
{
    public function handleRequest(string $route, string $method): void
    {
        $controller = new BookingController();

        switch ($route) {
            case '':
            case 'form':
                if ($method === 'POST') {
                    $controller->submitForm();
                } else {
                    $controller->showForm();
                }
                break;

            case 'success':
                $controller->showSuccess();
                break;

            case 'migrate':
                $controller->migrateData();
                break;

            case 'set-storage':
                $controller->setStorageType();

            case 'bookings':
                $controller->showBookings();
                break;

            default:
                http_response_code(404);
                echo "Страница не найдена";
                break;
        }
    }
}