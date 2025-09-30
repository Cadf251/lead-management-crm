<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repositories\LoginRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Models\Services\DbConnectionGlobal;
use App\adms\Services\AuthUser;
use App\adms\Views\Services\LoadViewService;

/**
 * Opera o LOGIN, chama o formulário, verifica os dados, etc.
 */
class Login
{
  private array|string|null $data = null;
  /**
   * Esse método controla o fluxo inicial da página se login.
   * Por padrão, envia para o formulário de login, porém, se tiver uma requisição
   * POST, tenta efetuar o login.
   */
  public function index()
  {
    // Se já tiver logado, manda para o dashboard
    if (AuthUser::logado()){
      header("Location: {$_ENV['HOST_BASE']}dashboard");
      exit;
    }

    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Verifica se o POST corresponde ao formulário de LOGIN
    if(isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_login", $this->data["form"]["csrf_token"])){
      // Se tiver OK, chama o método login.
      $this->login();
    } else {
      // Carregar a VIEW
      $loadView = new LoadViewService("adms/Views/login/login", [
        "title" => "Login",
        "css" => ["public/adms/css/login.css"]
      ]);
      $loadView->loadViewLogin();
    }
  }

  /**
   * Verifica se o login está correto
   */
  public function login() :void
  {
    // Conectar com o servidor GLOBAL
    $globalConn = new DbConnectionGlobal();
    $globalConn = $globalConn->conexao;

    // Encontrar o servidor do cliente
    $login = new LoginRepository($globalConn);
    $servidor = $login->verificarServidor((int)$this->data["form"]["servidor_id"]);

    // Verifica a conexão
    if ($servidor === false)
      $this->falha(["servidor" => $servidor]);
    
    // Tenta conectar com esse servidor
    $clienteConn = new DbConnectionClient($servidor);
    $clienteConn = $clienteConn->conexao;

    // Encontrar o usuário
    $loginNew = new LoginRepository($clienteConn);
    $usuario = $loginNew->verificarUsuario($this->data["form"]["usuario_email"]);

    if ($usuario === false)
      $this->falha(["usuario" => $usuario]);

    // Verificar senha
    $senha = $this->verificarSenha($this->data["form"]["usuario_senha"], $usuario["senha"]);

    if ($senha === false)
      $this->falha(["senha" => $senha]);

    // Faz a conexão e passa tudo para o $_SESSION
    $this->createSession($servidor, $usuario);

    // Verifica as permissões do usuário e atribui na SESSION
    $loginNew->verificarPermissoes($_SESSION["nivel_acesso_id"]);
    
    // Joga para o dashboard
    header("Location: {$_ENV['HOST_BASE']}dashboard");
    exit;
  }

  /**
   * Cria um log de erro, passa um alerta e redireciona para o login novamente
   * 
   * @param array $addLog Um array adicional e opcional para ir no log
   */
  public function falha(array $addLog = [])
  {
    GenerateLog::generateLog("warning", "O login falhou", [$this->data["form"], $addLog]);

    // Prepara o setWarning
    $_SESSION["alerta"] = [
      "O login falhou!",
      "O usuário não existe ou esta desativado."
    ];

    header("Location: {$_ENV['HOST_BASE']}");
    exit;
  }

  /**
   * Verifica se a senha do usuário está correta
   * 
   * @param string $senhaForm A senha informada no formulário
   * @param string $senhaBase A senha no db
   * 
   * @return bool|array Falso se não existir, array simplificado se existir
   */
  public function verificarSenha(string $senhaForm, string $senhaBase)
  {
    return password_verify($senhaForm, $senhaBase);
  }

  /**
   * Caso login suceder, recebe as informações e criar a SESSION 
   * 
   * @param array $servidor
   * @param array $usuario
   */
  public function createSession(array $servidor, array $usuario):void
  {
    // Inicia o repositório de níveis de acesso para colocar no SESSION também
    $_SESSION = array_merge(
      $_SESSION, [
        "logado" => true,
        "usuario_id" => (int)$usuario["id"],
        "usuario_nome" => $usuario["nome"],
        "usuario_email" => $usuario["email"],
        "foto_perfil" => $usuario["foto_perfil"],
        "nivel_acesso_id" => (int)$usuario["nivel_acesso_id"],
        "servidor_id" => (int)$this->data["form"]["servidor_id"],
        "db_credenciais" => $servidor
      ]
    );
  }
}