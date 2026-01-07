<?php

namespace App\adms\Helpers;

class Formatter
{
  public static function name(string $name): string
  {
    // Remove espaços extras e caracteres inválidos
    $name = trim($name);
    $name = preg_replace('/[^a-zA-ZÀ-ÿ\'\s]/u', '', $name); // permite acentos, hífen e apóstrofo
    $name = preg_replace('/\s+/', ' ', $name); // espaços múltiplos -> um só
    $name = strtolower($name);

    // Lista de palavras que devem ficar minúsculas
    $minusculas = ['da', 'de', 'do', 'das', 'dos', 'e'];

    // Formata cada parte do name
    $partes = explode(' ', $name);
    foreach ($partes as &$parte) {
      if (!in_array($parte, $minusculas)) {
        $parte = ucfirst($parte);
      }
    }

    return implode(' ', $partes);
  }

  public static function phoneToInternational(string $phone): string
  {
    // Remove todos os caracteres não numéricos
    $phone = preg_replace('/\D/', '', $phone);

    // Adiciona DDI 55 se não estiver presente
    if (strpos($phone, '55') !== 0) {
      $phone = '55' . $phone;
    }

    // Se não tiver pelo menos 12 dígitos com o DDI, consideramos inválido
    if (strlen($phone) < 12) return "";

    return $phone;
  }
}
