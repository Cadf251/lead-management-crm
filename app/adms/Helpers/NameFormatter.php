<?php

namespace App\adms\Helpers;

class NameFormatter
{
  public static function formatarNome(string $nome): string
  {
    return Formatter::name($nome);
  }

}