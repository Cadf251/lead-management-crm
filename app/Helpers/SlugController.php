<?php

namespace App\Helpers;

/**
 * Converte a controller enviada na URL no formato de class
 * 
 * @author Cadu <cadu.devmarketing@gmail.com>
 */
class SlugController
{

  public static function slugController(string $slugController):string
  {
    // Converte para lower
    $slugController = strtolower($slugController);
    $slugController = str_replace("-", " ", $slugController);
    
    // Converte a primeira leta de cada palavra
    $slugController = ucwords($slugController);

    $slugController = str_replace(" ", "", $slugController);

    return $slugController;
  }
}