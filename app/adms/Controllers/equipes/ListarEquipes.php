<?php

namespace App\adms\Controllers\equipes;

use App\adms\Controllers\erro\Erro;
use App\adms\Helpers\GenerateLog;
use App\adms\Presenters\EquipePresenter;
use Exception;

class ListarEquipes extends EquipesAbstract
{
  public function index()
  {
    try {
      $equipes = $this->repo->listarEquipes();
      $this->setData([
        "equipes" => EquipePresenter::present($equipes),
      ]);
      
      // Cria a VIEW
      $this->render("listar-equipes");
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR, [
        "info" => "Não foi possível carregar uma View/Presenter",
        "controller" => "ListarEquipes"
      ]);

      $error = new Erro();
      $error->index("500");
      exit;
    }
  }
}