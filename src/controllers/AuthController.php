<?php

use JetBrains\PhpStorm\NoReturn;

require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class AuthController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userRepository->createUsersTable();


        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showLoginForm(): void
    {
        $errors = [];
        require __DIR__ . '/../view/login.php';
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($username) || empty($password)) {
            $errors[] = "Логин и пароль обязательны для заполнения";
            require __DIR__ . '/../view/login.php';
            return;
        }

        $user = $this->userRepository->getUserByUsername($username);

        if (!$user || !$user->verifyPassword($password)) {
            $errors[] = "Неверный логин или пароль";
            require __DIR__ . '/../view/login.php';
            return;
        }


        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();


        header('Location: ?route=form');
        exit;
    }

    public function showRegisterForm(): void
    {
        $errors = [];
        require __DIR__ . '/../view/register.php';
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        // Валидация
        if (empty($username)) {
            $errors[] = "Логин обязателен для заполнения";
        } elseif (strlen($username) < 3) {
            $errors[] = "Логин должен содержать минимум 3 символа";
        }

        if (empty($password)) {
            $errors[] = "Пароль обязателен для заполнения";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль должен содержать минимум 6 символов";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Пароли не совпадают";
        }

        // Проверка существования пользователя
        $existingUser = $this->userRepository->getUserByUsername($username);
        if ($existingUser) {
            $errors[] = "Пользователь с таким логином уже существует";
        }

        if (!empty($errors)) {
            require __DIR__ . '/../view/register.php';
            return;
        }


        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User($username, $passwordHash);

        if (!$this->userRepository->createUser($user)) {
            $errors[] = "Ошибка при создании пользователя";
            require __DIR__ . '/../view/register.php';
            return;
        }

        // Перенаправляем на страницу входа
        header('Location: ?route=login');
        exit;
    }

    #[NoReturn] public function logout(): void
    {

        session_unset();
        session_destroy();


        header('Location: ?route=login');
        exit;
    }


    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }


    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }


    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ?route=login');
            exit;
        }
    }


    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::hasRole('admin')) {
            http_response_code(403);
            echo "Доступ запрещен. Требуются права администратора.";
            exit;
        }
    }
}