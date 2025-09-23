<?php

namespace App\adms\Helpers;

/**
 * Gerar e validar CSRF
 */
class CSRFHelper
{

  /**
   * Cria um token hexadecimal de 32 bytes e armazena em um array da SESSION => "csrf_tokens" => "(string){form_id}"
   */
  public static function generateCSRFToken(string $formId):string
  {
    $token = bin2hex(random_bytes(32));

    $_SESSION["csrf_tokens"][$formId] = $token;
    return $token;
  }

  /**
   * Valida um token
   */
  public static function validateCSRFToken(string $formId, string $token)
  {
    if ((isset($_SESSION["csrf_tokens"][$formId]))
      && (hash_equals($_SESSION["csrd_tokens"][$formId], $token))){
      
        unset($_SESSION["csrf_tokens"][$formId]);
        return true;
    } else 
      return false;
  }
}