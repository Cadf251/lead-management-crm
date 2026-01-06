<?php

namespace App\adms\Controllers\equipes;

use App\adms\Models\teams\Equipe;
use App\adms\Core\OperationResult;

class CongelarEquipe extends EquipeMain
{
  public function index(string|int|null $equipeId)
  {
    $this->main($equipeId);
  }

  public function executar(Equipe $equipe): OperationResult
  {
    return $this->service->pausar($equipe);
  }
}