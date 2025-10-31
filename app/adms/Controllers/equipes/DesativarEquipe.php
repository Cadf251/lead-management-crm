<?php

namespace App\adms\Controllers\equipes;

class DesativarEquipe extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $this->setInfoById((int)$equipeId);

    // Verifica se já está desativada
    if ($this->statusId === self::STATUS_DESATIVADO){
      $_SESSION["alerta"] = [
        "Aviso!",
        ["ℹ️ Essa equipe já está desativada."]
      ];
      $this->redirect();
    }

    // Congela o ID da equipe
    $congelar = $this->repo->desativar($this->id);

    if ($congelar)
      $_SESSION["alerta"] = [
        "Sucesso!",
        "✅ A equipe {$this->nome} foi desativada."
      ];
    else
      $_SESSION["alerta"] = [
        "Erro!",
        "❌ A equipe não {$this->nome} foi desativada."
      ];
    
      $this->redirect();
  }
}