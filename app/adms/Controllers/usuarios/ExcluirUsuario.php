<?php

namespace App\adms\Controllers\usuarios;

class ExcluirUsuario extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index(string|null $usuarioId)
  {
    // Seta o ID
    $this->data["usuario_id"] = (int)$usuarioId;
    $this->excluirUsuario();
  }

  /** Apaga permanentemente o usuário. */
  public function excluirUsuario():void
  {
    $excluir = $this->repo->excluir($this->data["usuario_id"]);

    if ($excluir === true){
      $_SESSION["alerta"] = [
        "Usuário excluído com sucesso!",
        "Seus dados agora são irrecuperáveis."
      ];
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi excluído.",
        "Tente novamente mais tarde."
      ];
    }
    header("Location: {$_ENV['HOST_BASE']}usuarios");
    exit;
  }
}