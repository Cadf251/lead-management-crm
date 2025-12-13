<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;

class ListarEquipes extends EquipesAbstract
{
  public function index()
  {
    $funcoes = $this->repo->selecionarOpcoes("equipes_usuarios_funcoes");

    $equipes = $this->repo->listarEquipes();
    $this->setData([
      "css" => ["public/adms/css/equipes.css"],
      "js" => ["public/adms/js/equipes.js?v=433422"],
      "equipes" => $equipes,
      "funcoes" => $funcoes
    ]);
    
    // Cria a VIEW
    $this->render("listar-equipes");
  }
}