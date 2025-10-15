<?php

namespace App\adms\Controllers\usuarios;

class DesativarUsuario extends UsuariosReciclagem
{
  public function index(string|null $usuarioId)
  {
    // Carrega o fluxo
    $this->fluxoPrincipal($usuarioId);
  }

  /** Desativa o usuário, seus tokens e retira-o de todas as equipes
   * 
   * @return bool Se retornar false, não deve continuar.
   */
  protected function executar(): bool
  {
    // Verifica se já está desativado
    if ($this->statusId === 2){
      $_SESSION["alerta"] = [
        "O usuário já está desativado.",
        "Você não pode desativar um usuário já desativado."
      ];
      return false;
    }
    
    $desativar = $this->repo->desativar($this->id);

    if ($desativar){
      $_SESSION["alerta"] = [
        "Usuário desativado com sucesso!",
        "Você ainda pode excluir permanentemente os seus dados."
      ];

      // Excluir a foto dele
      $this->apagarFoto();
      return true;
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi desativado.",
        "Tente novamente mais tarde."
      ];
      return false;
    }
  }
}