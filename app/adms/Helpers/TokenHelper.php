<?php

namespace App\adms\Helpers;

/**
 * Esta classe serve para criar TOKENs.
 * 
 * Para gerenciar Tokens, dê uma olhada em: App\adms\Repositories\TokenRepository.
 */
class TokenHelper
{
  /**
   * Cria um TOKEN de random_bytes(16)
   */
  public static function criarToken():string
  {
    return bin2hex(random_bytes(16));
  }
}