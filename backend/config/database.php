<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {

            $host = $_ENV['MYSQL_HOST'] ?? null;
            $dbname = $_ENV['MYSQL_DATABASE'] ?? null;
            $user = $_ENV['MYSQL_USER'] ?? null;
            $pass = $_ENV['MYSQL_PASSWORD'] ?? null;
            $port = $_ENV['DATA_PORT'] ?? 3306;

            if (!$host || !$dbname || !$user) {
                throw new Exception("Database environment variables not properly defined.");
            }

            $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false  //sql plus strict que PHP pour les types de données
                ]);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed.");
            }
        }

        return self::$pdo;
    }
}