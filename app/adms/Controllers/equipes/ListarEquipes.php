<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;

class ListarEquipes extends EquipesAbstract
{
  public function index()
  {
    $funcoes = $this->repo->selecionarOpcoes("equipes_usuarios_funcoes");

    $this->setData([
      "css" => ["public/adms/css/equipes.css"],
      "js" => ["public/adms/js/equipes.js?v=2"],
      "equipes" => $this->repo->listarEquipes(),
      "funcoes" => $funcoes
    ]);

    // Processa os usuários de cada equipe
    $usuarios = [];
    $proximos = [];
    foreach ($this->data["equipes"] as $equipe){
      $usuarios[$equipe["equipe_id"]] = $this->repo->listarUsuarios($equipe["equipe_id"]);
      $proximos[$equipe["equipe_id"]] = $this->repo->proximos($equipe["equipe_id"]);
    }

    // Inclui os usuários na VIEW
    $this->setData([
      "usuarios" => $usuarios,
      "proximos" => $proximos
    ]);
    
    // Cria a VIEW
    $this->render("listar-equipes");
  }
}