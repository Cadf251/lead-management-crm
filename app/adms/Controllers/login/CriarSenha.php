<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Core\LoadView;
use App\adms\Core\OperationResult;
use App\adms\Models\Token;
use App\adms\Services\TokenService;
use Exception;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class CriarSenha extends LoginAbstract
{
  public function index(int|string|null $param)
  {
    // Verifique o $param
    if (empty($param) && $param === null){
      $result = new OperationResult();
      $result->warning("O TOKEN expirou.");
      $result->report();
      $this->redirectLogin();
    }

    $parts = explode("-", $param);
    $servidorId = (int)$parts[0];
    $token = $parts[1];

    try {
      $this->connectClient($servidorId);

      // Token
      $tokenService = new TokenService($this->clientConn);

      try {
        $valido = $tokenService->validate($token, Token::TYPE_SYSTEM, Token::CONTEXT_CONFIRMAR_EMAIL);
      } catch (Exception $e) {
        throw new Exception("Não foi possível validar um TOKEN: " . $e->getMessage(), $e->getCode(), $e);
      }

      if ($valido === false) {
        $result = new OperationResult();
        $result->warning("O TOKEN expirou.");
        $result->report();
      }

      // Verifica se há POST antes de carregar a VIEW
      if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_confirmar", $this->data["form"]["csrf_token"])) {
        // Crie a nova senha, atualiza o status do usuário, desative o token e direcione para o dashboard
        $senhaHash = password_hash($this->data["form"]["usuario_senha"], PASSWORD_BCRYPT);
        
        $usuario = $this->usuarioRepository->selecionar($valido->getUserId());

        $result = $this->usuarioService->ativar($usuario, $senhaHash);

        $tokenService->disable($valido);

        $operation = new OperationResult();

        if($result->hadSucceded()){
          $this->fazerLogin($usuario);
        } else {
          $operation = new OperationResult();
          $operation->failed("Algo deu errado.");
          $operation->report();
        }
        
        $this->redirectLogin();
      }
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR);

      $operation = new OperationResult();
      $operation->failed("Algo deu errado.");
      $operation->report();

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