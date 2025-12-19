<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\CSRFHelper;

/** Manipula novos usuários. */
class CriarUsuario extends UsuariosAbstract
{
  /** Verifica se deve mostrar a VIEW do formulário ou apura os dados do $_POST */
  public function index()
  {
    // Seleciona as opções no banco de dados
    $optionsArray = $this->repo->sql->selecionarOpcoes("niveis_acesso");
    $optionsHTML = CreateOptions::criarOpcoes($optionsArray, null);

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
      
      $result = $this->service->criar(
        $usuario["nome"],
        $usuario["email"],
        $usuario["celular"],
        $usuario["nivel_acesso_id"]
      );

      // Mostrar mensagem de sucesso
      $_SESSION["alerta"] = [
        $result->getStatus(),
        $result->mensagens()
      ];
      $this->redirect();
    }

    // Carregar a VIEW
    $this->render("adms/Views/usuarios/criar-usuario");
  }
}