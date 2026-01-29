<?php

namespace App\adms\Controllers\login;

use App\adms\Controllers\base\ControllerBase;
use App\adms\Core\LoadView;
use App\adms\Database\DbConnectionClient;
use App\adms\Database\DbConnectionGlobal;
use App\adms\Services\LoginService;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
abstract class LoginBase extends ControllerBase
{
  protected string $viewFolder = "login";
  protected string $defaultView = "login";
  protected string $redirectPath = "login";

  private ?DbConnectionClient $clientConn = null;
  private LoginService $service;

  public function __construct()
  {
    $this->service = new LoginService();
  }

  public function getClientConn(): ?DbConnectionClient
  {
    return $this->clientConn;
  }

  public function getService(): LoginService
  {
    return $this->service;
  }
  
  protected function loadViewLogin($title = "Login", $to = "login")
  {
    $loadView = new LoadView("adms/Views/login/$to", [
      "title" => "Login"
    ]);
    $loadView->loadViewLogin();
  }
}
