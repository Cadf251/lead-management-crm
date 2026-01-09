<?php

namespace App\adms\Controllers\equipes;

use App\adms\Models\teams\Colaborador;
use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;

class MudarVez extends ColaboradorMain
{

  public function index(string|int $colaboradorId): void
  {
    $this->main($colaboradorId);
  }

  public function executar(Colaborador $colaborador, array $post): OperationResult
  {
    if ($post["task"] === "prejudicar") {
      $result = $this->service->prejudicar($colaborador);
    } else if ($post["task"] === "priorizar") {
      $result = $this->service->priorizar($colaborador);
    } else {
      $result = new OperationResult();
      $result->failed("Post mal formado.");
      return $result;
    }

    $equipe = $this->repo->selecionarEquipe((int)$post["equipe_id"]);

    if ($equipe !== null){
      $this->renderInfoBox($equipe);
    }

    return $result;
  }
}
