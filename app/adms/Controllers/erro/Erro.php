<?php

namespace App\adms\Controllers\erro;

use App\adms\Core\LoadView;

class Erro
{
  public function index(array|string|null $type) : void 
  {
    // Recebe qual é parametro de erro
    switch ($type){
      case "404":
        $this->erro404();
        break;
      case "500":
        $this->erro500();
        break;
      case "fatal":
        $this->fatal();
        break;
    }
  }

  public function erro404()
  {
    // Carregar a VIEW
    $loadView = new LoadView("adms/Views/erro/erro404", null);
    $loadView->loadExternalError();
  }

  public function erro500()
  {
    // Carregar a VIEW
    $loadView = new LoadView("adms/Views/erro/erro500", null);
    $loadView->loadExternalError();
  }

  public function fatal()
  {
    // Carregar a VIEW
    $loadView = new LoadView("adms/Views/erro/fatal", null);
    $loadView->loadView();
  }
}