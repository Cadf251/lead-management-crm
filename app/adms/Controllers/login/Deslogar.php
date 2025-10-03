<?php

namespace App\adms\Controllers\login;

use App\adms\Services\AuthUser;

class Deslogar
{
  public function index()
  {
    AuthUser::deslogar();
  }
}