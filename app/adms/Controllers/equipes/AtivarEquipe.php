<?php

namespace App\adms\Controllers\equipes;

class AtivarEquipe extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $this->setInfoById((int)$equipeId);

    // Verifica se já está ativa
    if ($this->statusId === self::STATUS_ATIVADO){
      $_SESSION["alerta"] = [
        "Aviso!",
        ["ℹ️ Essa equipe já está ativada."]
      ];
      $this->redirect();
    }

    // Congela o ID da equipe
    $congelar = $this->repo->ativar($this->id);

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