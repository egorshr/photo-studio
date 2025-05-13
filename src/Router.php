<?php

require_once __DIR__ . '/controllers/BookingController.php';

class Router {
    public function handleRequest(string $route, string $method): void {
        $controller = new BookingController();

        if ($route === '' || $route === 'form') {
            if ($method === 'POST') {
                $controller->submitForm();
            } else {
                $controller->showForm();
            }
        } elseif ($route === 'success') {
            $controller->showSuccess();
        } else {
            http_response_code(404);
            echo "Страница не найдена";
        }
    }
}
