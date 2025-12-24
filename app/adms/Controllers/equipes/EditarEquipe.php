<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Presenters\EquipePresenter;

class EditarEquipe extends EquipesAbstract
{
  public function index(string|null|int $equipeId)
  {
    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;
    $equipe = $this->repo->selecionarEquipe((int)$equipeId);
    
    if ($equipe === null) {
      $result = new OperationResult();
      $result->falha("Essa equipe não existe.");
      // Mostrar mensagem de erro
      $_SESSION["alerta"] = [
        $result->getStatus(),
        $result->mensagens()
      ];
      $this->redirect();
    }

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_equipe", $this->data["form"]["csrf_token"])) {

      $dados = $this->data["form"];

      $result = $this->service->editar($equipe, $dados);
      
      // Mostrar mensagem de sucesso
      $_SESSION["alerta"] = [
        $result->getStatus(),
        $result->mensagens()
      ];
      $this->redirect();
    }

    $optionsArray = $this->repo->sql->selecionarOpcoes("produtos");
    $this->setData([
      "title" => "Editar Equipe",
      "equipe" => EquipePresenter::present([$equipe]),
      "produtos-options" => CreateOptions::criarOpcoes($optionsArray, $equipe->produto->id)
    ]);

    $content = require APP_ROOT."app/adms/Views/equipes/editar-equipe.php";
    
    echo json_encode([
      "sucesso" => true,
      "html" => $content]);
    exit;
  }
}