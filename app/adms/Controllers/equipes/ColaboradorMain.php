<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\Colaborador;
use App\adms\Presenters\EquipePresenter;
use App\adms\UI\InfoBox;

abstract class ColaboradorMain extends EquipesAbstract
{
  protected string $fila = "";
  protected string $numero = "";

  abstract protected function executar(Colaborador $colaborador, array $post) :OperationResult;

  public function main(string|int|null $colaboradorId)
  {
    $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    $colaborador = $this->repo->selecionarUsuario((int)$colaboradorId);

    if ($colaborador === null) {
      $result = new OperationResult();
      $result->failed("Algo deu errado");
      echo json_encode($result->getForAjax());
      exit;
    }

    $result = $this->executar($colaborador, $post);
    
    echo json_encode([
      ...$result->getForAjax(),
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