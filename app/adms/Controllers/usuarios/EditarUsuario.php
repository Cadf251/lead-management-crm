<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CSRFHelper;
use App\adms\Models\NivelSistema;
use App\adms\Presenters\UsuarioPresenter;

class EditarUsuario extends UsuariosAbstract
{
  public function index(string|int $id)
  {
    $usuario = $this->repo->selecionar($id);

    // Seleciona as opções no banco de dados
    $optionsHTML = NivelSistema::getSelectOptions($usuario->getNivelAcessoId());

    $this->setData([
      "title" => "Editar Usuário | {$usuario->getNome()}",
      "usuarios" => UsuarioPresenter::present([$usuario]),
      "form-options" => $optionsHTML
    ]);

    // Verifica se há POST antes de carregar a VIEW
    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_usuario", $this->data["form"]["csrf_token"])) {
      
      $result = $this->service->editar($usuario, $this->data["form"]);

      $_SESSION["alerta"] = $result->getAlerta();

      $this->redirect();
    }

    $content = require APP_ROOT."app/adms/Views/usuarios/editar-usuario.php";

    echo json_encode([
      "sucesso" => true,
      "html" => $content]);
    exit;
  }
}
