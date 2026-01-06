<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\teams\Colaborador;

class AlterarRecebimento extends ColaboradorMain
{
  public function index(string|int $colaboradorId)
  {
    $this->main($colaboradorId);
  }

  public function executar(Colaborador $colaborador, array $post): OperationResult
  {
    if (!isset($post["recebe_leads"])) {
      $result = new OperationResult();
      $result->falha("Algo deu errado");
      echo json_encode([
        "sucesso" => $result->sucesso(),
        "alerta" => $result->getStatus(),
        "mensagens" => $result->getMensagens(),
      ]);
      exit;
    }

    $equipe = $this->repo->selecionarEquipe((int)$post["equipe_id"]);

    $set = $post["recebe_leads"];
    if ($set === "false") $set = false;
    else if ($set === "true") $set = true;
    $result = $this->service->alterarRecebimentoDeLeads($equipe, $colaborador, $set);

    $equipe = $this->repo->selecionarEquipe((int)$post["equipe_id"]);

    $this->renderInfoBox($equipe);
    
    return $result;
  }
}
