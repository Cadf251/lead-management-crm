<?php

namespace App\adms\Controllers\usuarios;

class AtivarUsuario extends UsuariosReciclagem
{
  public function index(string|null $usuarioId)
  {
    $this->enviarEmail = true;
    $this->fluxoPrincipal($usuarioId);
  }

  /** Ativa o usuário */
  public function executar():bool
  {
    // Verifica se o usuário não está desativado
    if ($this->statusId !== 2){
      $_SESSION["alerta"] = [
        "Este usuário não está desativado.",
        "Você não pode ativar um usuário que não esteja desativado."
      ];
      return false;
    }

    $ativar = $this->repo->ativar($this->id);

    if ($ativar){
      $_SESSION["alerta"] = [
        "Usuário ativado com sucesso!",
        "Ele deverá criar uma nova senha."
      ];
      return true;
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi ativado.",
        "Tente novamente mais tarde."
      ];
      return false;
    }
  }
}