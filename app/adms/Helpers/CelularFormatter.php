<?php

namespace App\adms\Helpers;

/**
 * Usada para converter strings de celulares.
 */
class CelularFormatter
{

  /**
   * Passa um celular no formato internacional para um placeholder
   * 
   * @var string $celular Ex: "55119XXXXXXXX"
   * @return string Ex: "(11)9XXXX-XXXX"
   */
  public static function paraPlaceholder(string $celular) :string|null
  {
    if (empty($celular) || ($celular === null)) return null;
    // Remove DDI 55 se estiver presente
    if (strpos($celular, '55') === 0)
      $celular = substr($celular, 2);

    $len = strlen($celular);

    if ($len === 11) {
      $ddd = substr($celular, 0, 2);
      $parte1 = substr($celular, 2, 5);
      $parte2 = substr($celular, 7, 4);
      return "($ddd)$parte1-$parte2";
    }

    if ($len === 10) {
      $ddd = substr($celular, 0, 2);
      $parte1 = substr($celular, 2, 4);
      $parte2 = substr($celular, 6, 4);
      return "($ddd)$parte1-$parte2";
    }

    // Retorna o número como está, se for inválido
    return $celular;
  }

  /**
   * Passa um celular no formato placeholder para um internacional
   * 
   * @var string $celular Ex: "(11)9XXXX-XXXX"
   * @return string Ex: "55119XXXXXXXX"
   */
  public static function paraInternaciona(string $celular):string|null
  {
    if (empty($celular) || $celular === null) return null;

    // Remove todos os caracteres não numéricos
    $celular = preg_replace('/\D/', '', $celular);

    // Adiciona DDI 55 se não estiver presente
    if (strpos($celular, '55') !== 0) {
      $celular = '55' . $celular;
    }

    // Se não tiver pelo menos 12 dígitos com o DDI, consideramos inválido
    if (strlen($celular) < 12) return null;

    return $celular;
  }
}