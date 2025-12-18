<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Presenters\UsuarioPresenter;

/** Carrega a VIEW gerenciar-usuarios */
class ListarUsuarios extends UsuariosAbstract
{
  /** Recupera os dados dos usuários do repositório [listar()], depois carrega a view passando os dados */
  public function index()
  {
    $usuarios = $this->repo->listar();

    $this->setData([
      "usuarios" => UsuarioPresenter::present($usuarios)
    ]);

    // Carregar a VIEW
    $view = "adms/Views/usuarios/listar-usuarios";
    $this->render($view);
  }
}