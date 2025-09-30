<?php

namespace App\adms\Helpers;

class SafeEcho
{
  
  public static function safeEcho(string $string) :string
  {
    return isset($string)
      ? $string
      : "";
  }
}