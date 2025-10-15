<?php

namespace App\adms\Controllers\equipes;

use App\adms\Views\Services\LoadViewService;

class Equipes extends EquipesAbstract
{
  /** @var array $data */
  private array $data = [];

  public function index()
  {
    // Pega o repositório
    $this->data["equipes"] = $this->repo->listarEquipes();

    // Cria a VIEW
    $this->data["title"] = "Equipes";
    $load = new LoadViewService("adms/Views/equipes/gerenciar-equipes", $this->data);
    $load->loadView();
  }
}