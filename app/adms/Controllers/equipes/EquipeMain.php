<?php

namespace App\adms\Controllers\equipes;

use App\adms\Core\OperationResult;
use App\adms\Models\teams\Equipe;

/** Fluxo principal */
abstract class EquipeMain extends EquipesAbstract
{
  abstract protected function executar(Equipe $equipe) :OperationResult;

  public function main(string|int|null $equipeId, $renderizeCard = true)
  {
    // Instancia a equipe
    $equipe = $this->repo->selecionarEquipe((int)$equipeId);

    if ($equipe === null) {
      $result = new OperationResult();
      $result->falha("Essa equipe não existe.");
      $_SESSION["alerta"] = $result->getAlerta();
      echo json_encode(["sucesso" => false]);
      exit;
    }

    $result = $this->executar($equipe);
    $html = "";
    
    if ($renderizeCard) {
      $html = $this->renderCard($equipe);
    }

    echo json_encode([
      "sucesso" => $result->sucesso(),
      "alerta" => $result->getStatus(),
      "mensagens" => $result->getMensagens(),
      "html" => $html]);
    exit;
  }
}