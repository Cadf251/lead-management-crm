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
        "Aviso!",
        ["❌ Você não pode excluir um usuário ativo."]
      ];
      return false;
    }

    $excluir = $this->repo->excluir($this->id);

    if ($excluir === true) {
      $_SESSION["alerta"] = [
        "Sucesso!",
        ["✅ Usuário excluído com sucesso."]
      ];
      return true;
    } else {
      $_SESSION["alerta"] = [
        "Erro!",
        ["❌ Algo deu errado."]
      ];
      return false;
    }
  }
}
