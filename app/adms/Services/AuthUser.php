<?php

namespace App\adms\Services;

/**
 * Regra de negócio criada para verificar se o usuário está logado ou não e direcionar para o login caso não esteja.
 */
class AuthUser
{
  public static function logado()
  {
    if ($_SESSION["logado"])
      return true;
    else {
      return false;
    }
  }

  public static function deslogar()
  {
    $_SESSION = [];
    header("Location: {$_ENV['HOST_BASE']}login");
    exit;
  }
}