<?php


namespace App\adms\Controllers\usuarios;

/** Serve para reenviar o e-mail de confirmação recuperando o token do usuário. */
class ReenviarEmail extends UsuariosReciclagem
{
  public function index(string|int|null $usuarioId)
  {
    $this->enviarEmail = true;
    $this->fluxoPrincipal($usuarioId);
  }

  protected function executar():bool
  {
    // Verifica se o usuário está desativado
    if (!$this->statusId === 2){
      $_SESSION["alerta"] = [
        "O usuário não tem um TOKEN para criar senha",
        ""
      ];
      return false;
    } else {
      return true;
    }
  }
}