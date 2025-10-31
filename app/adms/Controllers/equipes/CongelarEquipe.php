<?php

namespace App\adms\Controllers\equipes;

class CongelarEquipe extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $this->setInfoById((int)$equipeId);

    // Precisa estar ativada
    if ($this->statusId !== self::STATUS_ATIVADO){
      $_SESSION["alerta"] = [
        "Aviso!",
        "ℹ️ A equipe não está ativada."
      ];
    }

    // Congela o ID da equipe
    $congelar = $this->repo->congelar($this->id);

    if ($congelar)
      $_SESSION["alerta"] = [
        "Sucesso!",
        "✅ A equipe {$this->nome} foi ativada."
      ];
    else
      $_SESSION["alerta"] = [
        "Erro!",
        "❌ A equipe não {$this->nome} foi ativada."
      ];
    
      $this->redirect();
  }
}