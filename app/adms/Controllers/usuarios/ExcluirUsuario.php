<?php

namespace App\adms\Controllers\usuarios;

class ExcluirUsuario extends UsuariosReciclagem
{
  public function index(string|null $usuarioId)
  {
    $this->fluxoPrincipal($usuarioId);
  }

  /** Apaga permanentemente o usuário. */
  protected function executar(): bool
  {
    // Verifica se ele não está desativado
    if ($this->statusId !== 2) {
      $_SESSION["alerta"] = [
        "O usuário não está desativado.",
        "Você não pode excluir um usuário ativo."
      ];
      return false;
    }

    $excluir = $this->repo->excluir($this->id);

    if ($excluir === true) {
      $_SESSION["alerta"] = [
        "Usuário excluído com sucesso!",
        "Seus dados agora são irrecuperáveis."
      ];
      return true;
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi excluído.",
        "Tente novamente mais tarde."
      ];
      return false;
    }
  }
}
