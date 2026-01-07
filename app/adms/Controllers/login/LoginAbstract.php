<?php

namespace App\adms\Controllers\login;

use App\adms\Database\DbConnectionClient;
use App\adms\Database\DbConnectionGlobal;
use App\adms\Models\Usuario;
use App\adms\Repositories\LoginRepository;
use App\adms\Repositories\TokenRepository;
use App\adms\Repositories\UsuariosRepository;
use App\adms\Services\UsuariosService;
use Exception;
use PDO;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
abstract class LoginAbstract
{
  protected array|string|null $data = null;

  private ?PDO $globalConn;
  private LoginRepository $globalRepo;

  protected array|false $clientCredenciais;
  protected ?PDO $clientConn;
  protected LoginRepository $clientRepo;

  protected UsuariosRepository $usuarioRepository;
  protected UsuariosService $usuarioService;

  protected TokenRepository $tokenRepository;

  public function __construct()
  {
    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Instancia o servidor e repositório global
    $globalConn = new DbConnectionGlobal();
    $this->globalConn = $globalConn->conexao;
    $this->globalRepo = new LoginRepository($this->globalConn);
  }

  public function connectClient(int $servidorId)
  {
    $this->clientCredenciais = $this->globalRepo->verificarServidor($servidorId);

    if($this->clientCredenciais === false){
      throw new Exception("Servidor inválido");
    }

    $clienteConn = new DbConnectionClient($this->clientCredenciais);
    $this->clientConn = $clienteConn->conexao;
    $this->clientRepo = new LoginRepository($this->clientConn);
    $this->usuarioRepository = new UsuariosRepository($this->clientConn);
    $this->usuarioService = new UsuariosService($this->clientConn);
  }

  public function selecionarUsuario(string $email):?Usuario
  {
    $usuario = $this->usuarioRepository->selecionarByEmail($email);

    if($usuario === null){
      throw new Exception("Usuário não existe");
    }

    return $usuario;
  }

  public function fazerLogin(Usuario $usuario):void
  {
    // Faz a conexão e passa tudo para o $_SESSION
    $this->createSession($this->clientCredenciais, (int)$this->data["form"]["servidor_id"], $usuario);

    setcookie("codigo_empresa", $_SESSION["auth"]["servidor_id"]);

    // Joga para o dashboard
    $this->redirectDashboard();
  }

  public function redirectDashboard()
  {
    header("Location: {$_ENV['HOST_BASE']}dashboard");
    exit;
  }

  public function redirectLogin()
  {
    header("Location: {$_ENV['HOST_BASE']}login");
    exit;
  }

  /**
   * Caso login suceder, recebe as informações e criar a SESSION 
   * 
   * @param array $servidor
   * @param int $servidorId
   * @param Usuario $usuario
   */
  public function createSession(array $servidor, int $servidorId, Usuario $usuario):void
  {
    // Inicia o repositório de níveis de acesso para colocar no SESSION também
    $_SESSION["auth"] = [
      "logado" => true,
      "usuario_id" => $usuario->getId(),
      "usuario_nome" => $usuario->getNome(),
      "usuario_email" => $usuario->getEmail(),
      "foto_perfil_tipo" => $usuario->getFoto(),
      "nivel_acesso_id" => (int)$usuario->getNivelAcessoId(),
      "servidor_id" => $servidorId,
      "db_credenciais" => $servidor
    ];
  }
}