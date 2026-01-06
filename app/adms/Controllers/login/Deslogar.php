<?php

namespace App\adms\Controllers\login;

use App\adms\Core\AppContainer;
use App\adms\Services\AuthUser;

class Deslogar
{
  public function index()
  {
    AppContainer::getAuthUser()->deslogar();
  }
}