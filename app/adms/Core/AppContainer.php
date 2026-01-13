<?php

namespace App\adms\Core;

use App\adms\Database\DbConnectionClient;
use App\adms\Services\AuthUser;
use PDO;

class AppContainer
{
  private static ?AuthUser $authUser = null;
  private static ?DbConnectionClient $conn = null;

  public static function setAuthUser(AuthUser $instance): void
  {
    self::$authUser = $instance;
  }

  public static function getAuthUser():AuthUser
  {
    if (self::$authUser === null){
      return AuthUser::createFalse();
    }
    
    return self::$authUser;
  }

  public static function setClientConn(DbConnectionClient $conn): void
  {
    self::$conn = $conn;
  }

  public static function getClientConn(): PDO
  {
    if (self::$conn->conexao === null) {
      self::setClientConn(new DbConnectionClient());
    }

    return self::$conn->conexao;
  }
}