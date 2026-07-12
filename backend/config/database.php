<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {

            // Récupère les variables (on teste $_ENV puis getenv par sécurité pour Heroku)
            $host   = $_ENV['MYSQL_HOST'] ?? getenv('MYSQLHOST') ?? null;
            $dbname = $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQLDATABASE') ?? null;
            $user   = $_ENV['MYSQL_USER'] ?? getenv('MYSQLUSER') ?? null;
            $pass   = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQLPASSWORD') ?? null;
            $port   = $_ENV['MYSQL_PORT'] ?? getenv('MYSQLPORT') ?? $_ENV['DATA_PORT'] ?? 3306;

            if (!$host || !$dbname || !$user) {
                throw new Exception("Database environment variables not properly defined.");
            }

            $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, // sql plus strict que PHP pour les types de données
                    PDO::ATTR_TIMEOUT => 3               // Timeout à 3 secondes 
                ]);
            } catch (PDOException $e) {
                // Ajoute le message d'origine dans les logs Heroku pour voir la vraie panne
                error_log("Erreur de connexion SQL : " . $e->getMessage()); 
                
                throw new Exception("Database connection failed.");
            }
         }
        return self::$pdo;
    }
}