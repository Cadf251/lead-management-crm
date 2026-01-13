<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;
use App\adms\Presenters\EquipePresenter;

class NovoColaborador extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $equipe = $this->repo->selecionarEquipe((int)$equipeId);

    // Verifica se há POST
    $this->data["form"] = filter_input_array(INPUT_POST);

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("add_usuario", $this->data["form"]["csrf_token"])){
      $result = $this->service->novoColaborador($equipe, $this->data["form"]);

      // Mostrar mensagem de sucesso
      $result->report();

      $this->redirect("listar-colaboradores/{$equipeId}");
    }
    
    if($equipe === null) {
      echo json_encode([
        "sucesso" => false,
      ]);
      exit;
    }

    $usuarios = $this->repo->eleitosAEquipe($equipe);

    $funcoes = $this->repo->sql->selecionarOpcoes("equipes_usuarios_funcoes");

    $this->setData([
      "usuarios" => EquipePresenter::presentNovoColaborador($usuarios),
      "funcoes" => CreateOptions::criarOpcoes($funcoes),
      "equipe_id" => (int)$equipeId
    ]);

    $content = require APP_ROOT."app/adms/Views/equipes/novo-colaborador.php";
    
    echo json_encode([
      "sucesso" => true,
      "html" => $content]);
    exit;
  }
}