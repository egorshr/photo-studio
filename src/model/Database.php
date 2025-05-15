<?php

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = 'db'; $db = 'mydb'; $user = 'user'; $pass = 'pass';
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function initDatabase(): void
    {
        $db = self::getConnection();


        $tableExists = $db->query("SHOW TABLES LIKE 'bookings'")->rowCount() > 0;

        if (!$tableExists) {
            $sql = "CREATE TABLE bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                service VARCHAR(255) NOT NULL,
                photographer VARCHAR(255) NOT NULL,
                date DATE NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )";
            $db->exec($sql);
        } else {
            $columnExists = $db->query("SHOW COLUMNS FROM bookings LIKE 'user_id'")->rowCount() > 0;
            if (!$columnExists) {
                $db->exec("ALTER TABLE bookings ADD COLUMN user_id INT NOT NULL DEFAULT 1");
                $db->exec("ALTER TABLE bookings ADD FOREIGN KEY (user_id) REFERENCES users(id)");
            }
        }
    }
}