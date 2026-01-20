<?php

namespace App\adms\Helpers;

class CreateOptions
{
  /**
   * 
   * Cria as opções de <options> para um <select> com base em um array vindo da função selecionarOpcoes da classe DbOperations.
   * @param $array Array vindo de selecionarOpcoes
   * 
   * @return string HTML
   */
  public static function criarOpcoes(array $array, int|null $id = null): string
  {
    $html = "";
    foreach ($array as $row) {
      $selected = $row["id"] === $id ? "selected" : "";

      $html .= <<<HTML
        <option value="{$row['id']}" $selected>{$row["nome"]}</option>
      HTML;
    }

    return $html;
  }

  /**
   * 
   * Cria as opções de <options> para um <select> com base em um array vindo da função selecionarOpcoes da classe DbOperations.
   * @param $array Array vindo de selecionarOpcoes
   * 
   * @return string HTML
   */
  public static function create(array $array, int|null $id = null): string
  {
    $html = "";
    foreach ($array as $row) {
      $selected = $row["id"] === $id ? "selected" : "";

      $html .= <<<HTML
        <option value="{$row['id']}" $selected>{$row["name"]}</option>
      HTML;
    }

    return $html;
  }
}
