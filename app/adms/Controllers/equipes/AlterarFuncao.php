<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Models\teams\Colaborador;

class AlterarFuncao extends ColaboradorMain
{
  public function index(string|int|null $colaboradorId)
  {
    $this->main($colaboradorId);
  }

  public function executar(Colaborador $colaborador, $post): OperationResult
  {
    if (!isset($post["funcao_id"])) {
      $result = new OperationResult();
      $result->failed("Algo deu errado");
      echo json_encode($result->getForAjax());
      exit;
    }

    $novaFuncaoId = (int)$post["funcao_id"];
    
    return $this->service->alterarFuncao($colaborador, $novaFuncaoId);
  }
}
