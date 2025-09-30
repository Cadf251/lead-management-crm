<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Views\Services\LoadViewService;

class CriarUsuario
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index()
  {
    // Não precisa usar o repositório, passa null por padrão
    $this->data = [
      "title" => "Criar Usuário",
      "css" => ["public/adms/css/usuarios.css"],
      "usuarios" => null,
      "task" => "criar"
    ];

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/criar-usuario", $this->data);
    $loadView->loadView();
  }
}