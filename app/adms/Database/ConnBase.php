<?php

namespace App\adms\Database;

use PDO;
use App\adms\Helpers\GenerateLog;
use App\database\Models\Database;
use Exception;
use PDOException;

trait ConnBase
{
  protected static ?PDO $connection = null;
  protected static ?Database $db = null;

  public function __construct()
  {
    self::get();
  }

  private static function connect()
  {
    if (self::$db === null) {
      self::$db = self::getDb();
    }

    try {
      if (!self::$db->canConnect()) {
        throw new Exception("DB cannot connect.");
      }

      $host = self::$db->getHost();
      $port = self::$db->getPort();
      $name = self::$db->getName();

      self::$connection = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4;dbname={$name}",
        self::$db->getUser(),
        self::$db->getPass()
      );
    } catch (PDOException $e) {
      GenerateLog::log($e, GenerateLog::CRITICAL);
      http_response_code(500);
      exit;
    }
  }

  public static function get(): PDO
  {
    if (self::$connection === null) {
      self::connect();
    }

    return self::$connection;
  }
}