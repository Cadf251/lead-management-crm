<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Core\LoadView;
use App\adms\Services\AuthUser;
use Exception;

/**
 * Opera o LOGIN, chama o formulário, verifica os dados, etc.
 */
class Login extends LoginAbstract
{
  /**
   * Esse método controla o fluxo inicial da página se login.
   * Por padrão, envia para o formulário de login, porém, se tiver uma requisição
   * POST, tenta efetuar o login.
   */
  public function index()
  {
    // Se já tiver logado, manda para o dashboard
    if (AuthUser::logado()){
      $this->redirectDashboard();
    }

    // Verifica se o POST corresponde ao formulário de LOGIN
    if(isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_login", $this->data["form"]["csrf_token"])){
      try {
        $this->login();
      } catch (Exception $e){
        $this->falha([
          "error" => $e->getMessage()
        ]);
      }
    } else {
      $loadView = new LoadView("adms/Views/login/login", [
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
    try {
      $this->connectClient((int)$this->data["form"]["servidor_id"]);

      // Encontrar o usuário
      $usuario = $this->selecionarUsuario($this->data["form"]["usuario_email"]);

      // Verificar senha
      $senha = $this->verificarSenha($this->data["form"]["usuario_senha"], $usuario->senhaHash);

      if ($senha === false){
        throw new Exception("Usuário não existe");
      }

      // Faz a conexão e passa tudo para o $_SESSION
      $this->createSession($this->clientCredenciais, (int)$this->data["form"]["servidor_id"], $usuario);
      $this->permissoesSession($usuario->nivel->id, $usuario->id);
      
      // Joga para o dashboard
      $this->redirectDashboard();
    } catch (Exception $e){
      throw new Exception($e->getMessage());
    }
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
}