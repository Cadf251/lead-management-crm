<?php

namespace App\adms\Controllers\usuarios;

class DesativarUsuario extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index(string|null $usuarioId)
  {
    // Seta o ID
    $this->data["usuario_id"] = (int)$usuarioId;
    $this->desativarUsuario();
  }

  /** Desativa o usuário, seus tokens e retira-o de todas as equipes */
  public function desativarUsuario():void
  {
    $desativar = $this->repo->desativar($this->data["usuario_id"]);

    if ($desativar){
      $_SESSION["alerta"] = [
        "Usuário desativado com sucesso!",
        "Você ainda pode excluir permanentemente os seus dados."
      ];

      // Excluir a foto dele
      $this->apagarFoto($this->data["usuario_id"]);
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi desativado.",
        "Tente novamente mais tarde."
      ];
    }
    header("Location: {$_ENV['HOST_BASE']}usuarios");
    exit;
  }
}