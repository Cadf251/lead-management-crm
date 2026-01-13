<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Presenters\UsuarioPresenter;

/** 
 * ✅ FUNCIONAL - CUMPRE V1
 * 
 * Carrega a VIEW gerenciar-usuarios 
 */
class ListarUsuarios extends UsuariosAbstract
{
  /** Recupera os dados dos usuários do repositório [listar()], depois carrega a view passando os dados */
  public function index():void
  {
    $usuarios = $this->repo->listar();

    $this->setData([
      "usuarios" => UsuarioPresenter::present($usuarios)
    ]);

    // Carregar a VIEW
    $this->render();
  }
}