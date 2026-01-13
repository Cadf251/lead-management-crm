<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Controllers\base\CriarBase;
use App\adms\Core\AppContainer;
use App\adms\Helpers\CSRFHelper;
use App\adms\Models\NivelSistema;
use App\adms\Services\UsuariosService;

/** 
 * ✅ FUNCIONAL - CUMPRE V1
 * 
 * Manipula novos usuários.
 */
class CriarUsuario extends UsuariosAbstract
{
  protected string $viewFolder = "usuarios"; // Aqui você recuperou o "UsuarioAbstract"
  protected string $viewFile = "criar-usuario";
  protected string $csrfKey = "form_usuario";
  
  /** Verifica se deve mostrar a VIEW do formulário ou apura os dados do $_POST */
  public function index(): void
  {
    // Seleciona as opções no banco de dados
    $optionsHTML = NivelSistema::getSelectOptions();

    $this->setData([
      "title" => "Criar Usuário",
      "usuario" => null,
      "form-options" => $optionsHTML
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = $_POST;

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_usuario", $this->data["form"]["csrf_token"])) {
      // Resume o array para facilitar
      $usuario = $this->data["form"];

      $service = new UsuariosService(AppContainer::getClientConn());

      $result = $service->criar(
        $usuario["nome"],
        $usuario["email"],
        $usuario["celular"],
        $usuario["nivel_acesso_id"]
      );

      // Mostrar mensagem de sucesso
      $result->report();

      $this->redirect();
    }

    // Retorna a VIEW
    $content = require APP_ROOT . "app/adms/Views/usuarios/criar-usuario.php";

    echo json_encode([
      "sucesso" => true,
      "html" => $content
    ]);
    exit;
  }
}
