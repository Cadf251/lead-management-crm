<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Models\Equipe;
use App\adms\Models\EquipeUsuario;
use App\adms\Presenters\EquipePresenter;
use App\adms\UI\InfoBox;

abstract class ColaboradorMain extends EquipesAbstract
{
  protected string $fila = "";
  protected string $numero = "";

  abstract protected function executar(EquipeUsuario $colaborador, array $post) :OperationResult;

  public function main(string|int|null $colaboradorId)
  {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $colaborador = $this->repo->selecionarUsuario((int)$colaboradorId);

    if ($colaborador === null) {
      $result = new OperationResult();
      $result->falha("Algo deu errado");
      echo json_encode([
        "sucesso" => $result->sucesso(),
        "alerta" => $result->getStatus(),
        "mensagens" => $result->getMensagens(),
      ]);
      exit;
    }

    $result = $this->executar($colaborador, $post ?? []);
    
    echo json_encode([
      "sucesso" => $result->sucesso(),
      "alerta" => $result->getStatus(),
      "mensagens" => $result->getMensagens(),
      "fila" => $this->fila,
      "numero" => $this->numero
    ]);
    exit;
  }

  public function renderNumber(Equipe $equipe)
  {
    $this->numero = $equipe->countColaboradores();
  }

  public function renderInfoBox(Equipe $equipe)
  {
    $this->fila = EquipePresenter::fila($equipe)["infobox"];
  }
}