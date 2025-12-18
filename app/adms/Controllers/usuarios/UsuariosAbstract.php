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
 * Define funções universais para as classes de usuários. 
 * Tem o objetivo de ser herdado. 
 * Instancia o repositório automaticamente.
 */
abstract class UsuariosAbstract
{
  /** @var array $data Contém as informações da VIEW, evite usar no back-end. */
  protected array $data = [
    "title" => "Usuários",
    "css" => ["public/adms/css/usuarios.css"],
    "js" => []
  ];

  protected UsuariosService $service;
  protected UsuariosRepository $repo;
  protected TokenRepository $tokenRepo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de usuários e de tokens */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new UsuariosRepository($conn->conexao);
    $this->tokenRepo = new TokenRepository($conn->conexao);
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

  // /** 
  //  * Recupera os dados do usuário e seta na classe. Também trata os erros e faz o direcionamento caso falhe.
  //  * 
  //  * @param int $usuarioId O ID do usuário.
  //  */
  // public function setInfoById(int $usuarioId):void
  // {
  //   $this->id = (int)$usuarioId;
  //   $usuarioArray = $this->repo->selecionar($this->id);

  //   // Verifica se houve erro
  //   if (($usuarioArray === false) || (empty($usuarioArray))) {
  //     GenerateLog::generateLog("error", "A consulta ao repositório retornou falso ou vazio", ["id" => $usuarioId]);

  //     // Prepara o setWarning
  //     $_SESSION["alerta"] = [
  //       "Erro!",
  //       "❌ O usuário não existe ou foi excluído."
  //     ];

  //     $this->redirect();
  //   }

  //   // Simplifica o array
  //   $usuario = $usuarioArray[0];

  //   // Seta os parâmetros do usuário
  //   $this->nome = $usuario["u_nome"];
  //   $this->email = $usuario["u_email"];
  //   $this->nivId = $usuario["niv_id"];
  //   $this->statusId = $usuario["us_id"];

  //   // Seta como array também para passar para a VIEW se necessário
  //   $this->data["usuario"] = $usuario;
  // }

}
