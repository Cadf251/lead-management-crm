<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Models\Repositories\UsuariosRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Views\Services\LoadViewService;

class Usuarios
{

  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;
  public function index()
  {
    // Intancia o Repository para recuperar os registros de usuários
    $conn = new DbConnectionClient(null);
    $usuarios = new UsuariosRepository($conn->conexao);
    $this->data = [
      "title" => "Usuários",
      "css" => ["public/adms/css/usuarios.css"],
      "usuarios" => $usuarios->listar()
    ];

    // Carregar a VIEW
    $loadView = new LoadViewService("adms/Views/usuarios/gerenciar-usuarios", $this->data);
    $loadView->loadView();
  }
}