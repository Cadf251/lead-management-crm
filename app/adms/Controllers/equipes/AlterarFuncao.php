<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Models\EquipeUsuario;

class AlterarFuncao extends ColaboradorMain
{
  public function index(string|int|null $colaboradorId)
  {
    $this->main($colaboradorId);
  }

  public function executar(EquipeUsuario $colaborador, $post): OperationResult
  {
    if (!isset($post["funcao_id"])) {
      $result = new OperationResult();
      $result->falha("Algo deu errado");
      echo json_encode([
        "sucesso" => $result->sucesso(),
        "alerta" => $result->getStatus(),
        "mensagens" => $result->getMensagens(),
      ]);
      exit;
    }

    $novaFuncaoId = (int)$post["funcao_id"];
    
    return $this->service->alterarFuncao($colaborador, $novaFuncaoId);
  }
}
