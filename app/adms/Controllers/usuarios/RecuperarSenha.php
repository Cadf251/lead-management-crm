<?php

namespace App\adms\Controllers\usuarios;

class RecuperarSenha extends UsuariosReciclagem
{
  /** Recupera os dados para resetar a senha */
  public function index(string|int|null $usuarioId)
  {
    $this->enviarEmail = true;
    $this->fluxoPrincipal($usuarioId);
  }

  /**
   * Apaga a senha do usuário e retorna ao status de aguardando confirmação
   */
  public function executar():bool
  {
    // Verifica se não está ativo
    if ($this->statusId !== 3){
      $_SESSION["alerta"] = [
        "Aviso!",
        ["❌ Você não pode recuperar a senha de um usuário inativo."]
      ];
      return false;
    }

    $reset = $this->repo->resetarSenha($this->id);

    if ($reset === true){
      $_SESSION["alerta"] = [
        "Sucesso!",
        ["✅ A senha atual foi apagada para redefinição."]
      ];
      return true;
    } else {
      $_SESSION["alerta"] = [
        "Erro!",
        ["❌ Ocorreu algum erro inesperado."]
      ];
      return false;
    }
  }
}