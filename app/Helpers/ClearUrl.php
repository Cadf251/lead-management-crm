<?php

namespace App\Helpers;

/**
 * Limpar o URL recebido
 * 
 * @author Cadu
 */
class ClearUrl
{
  /**
   * Método static pode ser chamado diretamente na classe sem criar uma instância
   * Limpar a URL, eliminando TAG, espaços, barra final e caracteres especiais
   * 
   * @return string
   */
  public static function clearUrl(string $url) :string
  {
    // Elimina a barra final
    $url = rtrim($url, "/");

    // Arrays de caracteres não aceitos
    $unacceptedCharacters = [
      'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'ü', 'Ý', 'Þ', 'ß',
      'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ý', 'ý', 'þ', 'ÿ', 
      '"', "'", '!', '@', '#', '$', '%', '&', '*', '(', ')', '_', '+', '=', '{', '[', '}', ']', '?', ';', ':', '.', ',', '\\', '\'', '<', '>', '°', 'º', 'ª', ' '
    ];

    // Arrays de caracteres aceitos
    $acceptedCharacters = [
      'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'y', 'b', 's',
      'a', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'y', 'y', 'y',
      '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
    ];
    // Substituir os caracteres não aceitos pelos caracteres aceitos
    return str_replace(mb_convert_encoding($unacceptedCharacters, 'ISO-8859-1', 'UTF-8'), $acceptedCharacters, mb_convert_encoding($url, 'ISO-8859-1', 'UTF-8'));
  }

  
}