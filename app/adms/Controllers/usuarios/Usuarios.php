<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Views\Services\LoadViewService;

/** Carrega a VIEW gerenciar-usuarios */
class Usuarios extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  /** Recupera os dados dos usuários do repositório [listar()], depois carrega a view passando os dados */
  public function index()
  {
    $this->data = [
      "title" => "Usuários",
      "css" => ["public/adms/css/usuarios.css"],
      "usuarios" => $this->repo->listar()
    ];

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/gerenciar-usuarios", $this->data);
    $loadView->loadView();
  }
}