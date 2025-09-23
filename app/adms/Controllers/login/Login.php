<?php

namespace App\adms\Controllers\login;

use App\adms\Views\Services\LoadViewService;

class Login
{
  public function index()
  {
    // echo "Página de login carregada<br>";
    
    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/login/login", [
      "title" => "Login",
      "css" => ["public/adms/css/login.css"]
    ]);
    $loadView->loadViewLogin();
  }
}