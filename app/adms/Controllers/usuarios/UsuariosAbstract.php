<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Repositories\TokenRepository;
use App\adms\Repositories\UsuariosRepository;
use App\adms\Database\DbConnectionClient;
use App\adms\Core\LoadView;
use App\adms\Models\Usuario;
use App\adms\Presenters\UsuarioPresenter;
use App\adms\Services\UsuariosService;

/** 
 * ✅ FUNCIONAL - CUMPRE V1
 * 
 * Define funções universais para as classes de usuários. 
 * Tem o objetivo de ser herdado. 
 * Instancia o repositório automaticamente.
 */
abstract class UsuariosAbstract
{
  protected array $data = [
    "title" => "Usuários",
  ];

  protected UsuariosService $service;
  protected UsuariosRepository $repo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de usuários e de tokens */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new UsuariosRepository($conn->conexao);
    $this->service = new UsuariosService($conn->conexao);
  }

  /** 
   * Inclui no array $this->data valores adicionais que serão passados para o VIEW.
   */
  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  /** 
   * Retorna o $this->data.
   * 
   * @return array
   */
  protected function getData(): array
  {
    return $this->data;
  }

  /**
   * Instancia e carrega a view.
   * 
   * @param string $viewPath O caminho completo para a view
   */
  protected function render(string $viewPath): void
  {
    $loadView = new LoadView($viewPath, $this->getData());
    $loadView->loadView();
  }

  /**
   * Redireciona de volta para "listar usuário".
   */
  public function redirect(): void
  {
    header("Location: {$_ENV['HOST_BASE']}listar-usuarios");
    exit;
  }

  public function renderizarCard(Usuario $usuario){
    $usuarios = UsuarioPresenter::present([$usuario]);
    $usuario = $usuarios[0];
    return require APP_ROOT."app/adms/Views/usuarios/partials/usuario-card.php";
  }
}
