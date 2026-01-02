<?php

namespace App\adms\Controllers\equipes;

use App\adms\Models\EquipeUsuario;
use App\adms\Core\OperationResult;
use Exception;

class RemoverColaborador extends ColaboradorMain
{
  public function index(string|int $colaboradorId): void
  {
    $this->main($colaboradorId);
  }

  public function executar(EquipeUsuario $colaborador, array $post): OperationResult
  {
    $result = $this->service->removerColaborador($colaborador);

    $equipe = $this->repo->selecionarEquipe((int)$post["equipe_id"]);

    $this->renderNumber($equipe);
    $this->renderInfoBox($equipe);

    return $result;
  }
}