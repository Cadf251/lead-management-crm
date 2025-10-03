<?php

namespace App\adms\Controllers\login;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repositories\LoginRepository;
use App\adms\Models\Repositories\TokenRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Models\Services\DbConnectionGlobal;
use App\adms\Views\Services\LoadViewService;

/** Manipula a criação de novas senhas */
class NovaSenha
{
  private array|null $data;

  public function index(int|string|null $param)
  {
    // Se tiver POST do form CONFIRMAR, verifique:
    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Verifique o $param
    if (!empty($param) && $param !== null){
      $parts = explode("-", $param);
      $servidorId = (int)$parts[0];
      $token = $parts[1];

      // Conectar com o servidor GLOBAL
      $globalConn = new DbConnectionGlobal();
      $globalConn = $globalConn->conexao;

      // Encontrar o servidor do cliente
      $login = new LoginRepository($globalConn);
      $servidor = $login->verificarServidor($servidorId);

      if ($servidor !== false){
        // Tenta conectar com esse servidor
        $clienteConn = new DbConnectionClient($servidor);
        $clienteConn = $clienteConn->conexao;

        // Verifica o token pelo servidor do cliente
        $loginNew = new LoginRepository($clienteConn);
        $tokenRepo = new TokenRepository($clienteConn);
        $valido = $tokenRepo->tokenValido($token, "sistema", "confirmar_email_senha");
        
        if ($valido !== false){
          // Verifica se há POST antes de carregar a VIEW
          if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_confirmar", $this->data["form"]["csrf_token"])) {
            // Crie a nova senha, atualiza o status do usuário, desative o token e direcione para o dashboard
            $senhaHash = password_hash($this->data["form"]["usuario_senha"], PASSWORD_BCRYPT);
            
            $result = $loginNew->registrarSenha($senhaHash, $valido["usuario_id"]);
            
            if ($result === true){
              $desativar = $tokenRepo->desativarToken($token);

              if ($desativar === true){
                $_SESSION["alerta"] = [
                  "Senha criada com sucesso!",
                  ""
                ];
              } else {
                GenerateLog::generateLog("error", "Não foi possível desativar o TOKEN", ["token" => $token]);
                $_SESSION["alerta"] = [
                  "Algo deu errado!",
                  "Não foi possível criar a senha no momento."
                ];
              }
            } else {
              GenerateLog::generateLog("error", "Não foi possível registrar a senha", ["token" => $token]);
              $_SESSION["alerta"] = [
                "Algo deu errado!",
                "Não foi possível criar a senha no momento."
              ];
            }

            header("Location: {$_ENV['HOST_BASE']}login");
            exit;
          }

          // Carrega a VIEW se não tiver nenhum $_POST válido.
          $loadView = new LoadViewService("adms/Views/login/confirmar", [
            "title" => "Nova Senha",
            "css" => ["public/adms/css/login.css"]
          ]);
          $loadView->loadViewLogin();
          exit;
        } else {
          GenerateLog::generateLog("info", "Alguém tentou criar uma senha com um token inválido.", ["url" => $param]);
          $_SESSION["alerta"] = [
            "Token inválido!",
            "É necessário criar um novo."
          ];
          header("Location: {$_ENV['HOST_BASE']}login");
          exit;
        }
      }
    }

    // Se tiver POST do form NOVA-SENHA, verifique:
    // Se o E-MAIL é válido, então faz o processo de recuperação de senha.

    // Se o parâmetro for NULL, apenas carregue a VIEW perguntando qual é o E-MAIL do usuário.
    $loadView = new LoadViewService("adms/Views/login/nova-senha", [
      "title" => "Nova Senha",
      "css" => ["public/adms/css/login.css"]
    ]);
    $loadView->loadViewLogin();
  }
}