<?php
class Database {
  private static $pdo = null;
  
  public static function getConnection() {
    if (self::$pdo === null) {
      $host = $_ENV['MYSQL_HOST'];
      $dbname = $_ENV['MYSQL_DATABASE'];
      $user = $_ENV['MYSQL_USER'];
      $pass = $_ENV['MYSQL_PASSWORD'];
      $port = $_ENV['DATA_PORT'];

      $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

      self::$pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    }

      return self::$pdo;
  }
}