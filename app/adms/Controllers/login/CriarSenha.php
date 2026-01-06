<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Repositories\TokenRepository;
use App\adms\Core\LoadView;
use Exception;

class CriarSenha extends LoginAbstract
{
  private array $sucessMsg = [
    "✅ Sucesso!",
    "Senha criada com sucesso!"
  ];

  private array $failMsg = [
    "❌ Erro!",
    "Algo deu errado."
  ];

  private array $tokenFail = [
    "ℹ️ Atenção!",
    "O TOKEN expirou."
  ];

  public function index(int|string|null $param)
  {
    // Verifique o $param
    if (empty($param) && $param === null){
      $_SESSION["alerta"] = $this->tokenFail;
      $this->redirectLogin();
    }

    $parts = explode("-", $param);
    $servidorId = (int)$parts[0];
    $token = $parts[1];

    try {
      $this->connectClient($servidorId);

      // Verifica o token pelo servidor do cliente
      $this->tokenRepository = new TokenRepository($this->clientConn);
      $valido = $this->tokenRepository->tokenValido($token, "sistema", "confirmar_email_senha");
      
      if ($valido === false) {
        $_SESSION["alerta"] = $this->tokenFail;
        $this->redirectLogin();
      }

      // Verifica se há POST antes de carregar a VIEW
      if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_confirmar", $this->data["form"]["csrf_token"])) {
        // Crie a nova senha, atualiza o status do usuário, desative o token e direcione para o dashboard
        $senhaHash = password_hash($this->data["form"]["usuario_senha"], PASSWORD_BCRYPT);
        
        $usuario = $this->usuarioRepository->selecionar($valido["usuario_id"]);

        $result = $this->usuarioService->ativar($usuario, $senhaHash);

        $this->tokenRepository->desativarToken($token);

        if($result->sucesso()){
          $_SESSION["alerta"] = $this->sucessMsg;
          
          $this->fazerLogin($usuario);
        } else {
          $_SESSION["alerta"] = $this->failMsg;
        }
        
        $this->redirectLogin();
      }
    } catch (Exception $e){
      GenerateLog::generateLog("error", "Não foi possível gerar nova senha", [
        "error" => $e->getMessage()
      ]);
      $_SESSION["alerta"] = $this->failMsg;
      $this->redirectLogin();
    }
    
    // Carrega a VIEW se não tiver nenhum $_POST válido.
    $loadView = new LoadView("adms/Views/login/confirmar", [
      "title" => "Nova Senha",
      "css" => ["public/adms/css/login.css"]
    ]);
    $loadView->loadViewLogin();
  }
}