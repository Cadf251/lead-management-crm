<?php

namespace App\adms\Controllers\login;

use App\adms\Core\AppContainer;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class Deslogar
{
  public function index()
  {
    AppContainer::getAuthUser()->deslogar();
  }
}