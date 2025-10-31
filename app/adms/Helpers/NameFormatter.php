<?php

namespace App\adms\Helpers;

class NameFormatter
{
  public static function formatarNome(string $nome): string
  {
      // Remove espaços extras e caracteres inválidos
      $nome = trim($nome);
      $nome = preg_replace('/[^a-zA-ZÀ-ÿ\'\s]/u', '', $nome); // permite acentos, hífen e apóstrofo
      $nome = preg_replace('/\s+/', ' ', $nome); // espaços múltiplos -> um só
      $nome = strtolower($nome);

      // Lista de palavras que devem ficar minúsculas
      $minusculas = ['da', 'de', 'do', 'das', 'dos', 'e'];

      // Formata cada parte do nome
      $partes = explode(' ', $nome);
      foreach ($partes as &$parte) {
          if (!in_array($parte, $minusculas)) {
              $parte = ucfirst($parte);
          }
      }

      return implode(' ', $partes);
  }

}