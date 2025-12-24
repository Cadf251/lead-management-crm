<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Core\LoadView;
use App\adms\Core\OperationResult;
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
    if (AuthUser::logado()) {
      $this->redirectDashboard();
    }

    // Verifica se o POST corresponde ao formulário de LOGIN
    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_login", $this->data["form"]["csrf_token"])) {
      try {
        $this->verificarLogin();
      } catch (Exception) {
        // Instancia o warning
        $result = new OperationResult();
        $result->falha("O usuário não existe ou está desativado.");

        // Prepara o setWarning
        $_SESSION["alerta"] = [
          $result->getStatus(),
          $result->mensagens()
        ];

        $this->redirectLogin();
      }
    } else {
      $this->loadViewLogin();
    }
  }

  /**
   * Verifica se o login está correto
   */
  private function verificarLogin(): void
  {
    try {
      // Conecta com o servidor do cliente
      $this->connectClient((int)$this->data["form"]["servidor_id"]);

      // Encontrar o usuário
      $usuario = $this->selecionarUsuario($this->data["form"]["usuario_email"]);

      // Verifica se ele pode logar ou não
      if(!$usuario->podeLogar()) {
        throw new Exception("Usuário não está habilitado a logar.");
      }

      // Verificar senha
      $this->verificarSenha($this->data["form"]["usuario_senha"], $usuario->senhaHash);

      // Finaliza o Login
      $this->fazerLogin($usuario);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  private function loadViewLogin()
  {
    $loadView = new LoadView("adms/Views/login/login", [
      "title" => "Login",
      "css" => ["public/adms/css/login.css"]
    ]);
    $loadView->loadViewLogin();
  }

  /**
   * Verifica se a senha do usuário está correta
   * 
   * @param string $senhaForm A senha informada no formulário
   * @param string $senhaBase A senha no db
   * 
   * @return void
   */
  private function verificarSenha(string $senhaForm, string $senhaBase):void
  {
    if (password_verify($senhaForm, $senhaBase) === false) {
      throw new Exception("Senha inválida.");
    }
  }
}
