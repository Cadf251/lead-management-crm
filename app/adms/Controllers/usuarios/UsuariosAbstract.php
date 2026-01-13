<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Controllers\base\ControllerBase;
use App\adms\Repositories\UsuariosRepository;
use App\adms\Models\Usuario;
use App\adms\Presenters\UsuarioPresenter;
use App\adms\Services\UsuariosService;
use PDO;

/** 
 * ✅ FUNCIONAL - CUMPRE V1
 * 
 * Define funções universais para as classes de usuários. 
 * Tem o objetivo de ser herdado. 
 * Instancia o repositório automaticamente.
 */
abstract class UsuariosAbstract extends ControllerBase
{
  protected string $viewFolder = "usuarios";
  protected string $redirectPath = "listar-usuarios";
  protected string $defaultView = "listar-usuarios";
  
  protected array $data = ["title" => "Usuários"];
  protected UsuariosService $service;
  protected UsuariosRepository $repo;

  protected function boot(PDO $conexao): void
  {
    $this->repo = new UsuariosRepository($conexao);
    $this->service = new UsuariosService($conexao);
  }

  public function renderizarCard(Usuario $usuario)
  {
    $usuarios = UsuarioPresenter::present([$usuario]);
    $usuario = $usuarios[0];
    return require APP_ROOT . "app/adms/Views/usuarios/partials/usuario-card.php";
  }
}
