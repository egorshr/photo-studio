<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/User.php';

class UserRepository
{
    public function createUsersTable(): void
    {
        $db = Database::getConnection();

        $tableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;

        if (!$tableExists) {
            $sql = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            $db->exec($sql);
        }
    }

    public function createUser(User $user): bool
    {
        try {
            $db = Database::getConnection();

            $stmt = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            return $stmt->execute([
                $user->getUsername(),
                $user->getPasswordHash(),
                $user->getRole()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserByUsername(string $username): ?User
    {
        try {
            $db = Database::getConnection();

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);

            $userData = $stmt->fetch();

            if (!$userData) {
                return null;
            }

            return new User(
                $userData['username'],
                $userData['password_hash'],
                $userData['role'],
                $userData['id'],
                $userData['created_at']
            );
        } catch (PDOException) {
            return null;
        }
    }

    public function getUserById(int $id): ?User
    {
        try {
            $db = Database::getConnection();

            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $userData = $stmt->fetch();

            if (!$userData) {
                return null;
            }

            return new User(
                $userData['username'],
                $userData['password_hash'],
                $userData['role'],
                $userData['id'],
                $userData['created_at']
            );
        } catch (PDOException) {
            return null;
        }
    }
}
