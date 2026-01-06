<?php

namespace App\adms\Core;

use App\adms\Services\AuthUser;

class AppContainer
{
  private static array $instances = [];
  private static ?AuthUser $authUser = null;

  public static function set(string $key, mixed $value): void
  {
    self::$instances[$key] = $value;
  }

  public static function setAuthUser(AuthUser $instance): void
  {
    self::$authUser = $instance;
  }

  public static function get(string $key): mixed
  {
    return self::$instances[$key] ?? null;
  }

  public static function getAuthUser():AuthUser
  {
    if (self::$authUser === null){
      return AuthUser::createFalse();
    }
    
    return self::$authUser;
  }
}