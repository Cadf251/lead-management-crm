<?php

namespace App\adms\Controllers\equipes;

use App\adms\Presenters\EquipePresenter;

class ListarColaboradores extends EquipesAbstract
{
  public function index(string|int|null $equipeId)
  {
    $equipe = $this->repo->selecionarEquipe((int)$equipeId);

    $funcoes = $this->repo->sql->selecionarOpcoes("equipes_usuarios_funcoes");

    $this->setData([
      "title" => "Listar Colaboradores",
      "equipes" => EquipePresenter::present([$equipe], $funcoes),
    ]);

    $this->render("listar-colaboradores");
  }
}