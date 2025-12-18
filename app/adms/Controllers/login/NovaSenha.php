<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Core\LoadView;
use App\adms\Helpers\GenerateLog;
use Exception;

/** Manipula a criação de novas senhas */
class NovaSenha extends LoginAbstract
{
  public function index()
  {
    // Se tiver POST do form NOVA-SENHA, verifique:
    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_nova_senha", $this->data["form"]["csrf_token"])) {
      try {
        $this->connectClient((int)$this->data["form"]["servidor_id"]);

        // Encontrar o usuário
        $usuario = $this->selecionarUsuario($this->data["form"]["usuario_email"]);

        // Seta o SERVIDOR ID para que ele apareça no email de confirmação de senha
        $_SESSION["servidor_id"] = (int)$this->data["form"]["servidor_id"];

        // Falta enviar o e-mail de confirmação
        $result = $this->usuarioService->resetarSenha($usuario);

        unset($_SESSION["servidor_id"]);

        $_SESSION["alerta"] = [
          "✅ Sucesso!",
          $result->mensagens()
        ];
      } catch (Exception $e){
        $_SESSION["alerta"] = [
          "❌ Erro!",
          "Algo deu errado."
        ];
        GenerateLog::generateLog("error", "Não foi possível resetar uma senha no login", [
          "error" => $e->getMessage()
        ]);
      }

      $this->redirectLogin();
    }

    // Se não há $_POST, carregue a view
    $this->loadViewNovaSenha();
  }

  public function loadViewNovaSenha()
  {
    $loadView = new LoadView("adms/Views/login/nova-senha", [
      "title" => "Nova Senha",
      "css" => ["public/adms/css/login.css"]
    ]);
    $loadView->loadViewLogin();
  }
}