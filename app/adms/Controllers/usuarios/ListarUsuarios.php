<?php

namespace App\adms\Controllers\usuarios;

/** Carrega a VIEW gerenciar-usuarios */
class ListarUsuarios extends UsuariosAbstract
{
  /** Recupera os dados dos usuários do repositório [listar()], depois carrega a view passando os dados */
  public function index()
  {
    $this->setData([
      "usuarios" => $this->repo->listar()
    ]);

    // Carregar a VIEW
    $view = "adms/Views/usuarios/listar-usuarios";
    $this->render($view);
  }
}