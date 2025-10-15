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
        "Aviso!",
        ["❌ Você não pode ativar um usuário que não esteja desativado."]
      ];
      return false;
    }

    $ativar = $this->repo->ativar($this->id);

    if ($ativar){
      $_SESSION["alerta"] = [
        "Sucesso!",
        ["✅ O usuário foi ativado com sucesso."]
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