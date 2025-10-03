<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Models\Repositories\UsuariosRepository;
use App\adms\Models\Services\DbConnectionClient;

class AtivarUsuario extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  public function index(string|null $usuarioId)
  {
    // Seta o ID
    $this->data["usuario_id"] = (int)$usuarioId;

    // Recupera as informações do usuário
    $usuarios = $this->repo->selecionar($this->data["usuario_id"]);
    $usuario = $usuarios[0];
    $this->data["usuario_nome"] = $usuario["u_nome"];
    $this->data["usuario_email"] = $usuario["u_email"];
    $this->data["usuario_status_id"] = $usuario["us_id"];

    // Verifica se o usuário está desativado
    if ($this->data["usuario_status_id"] === 2)
      $this->ativarUsuario();
    else{
      $_SESSION["alerta"] = [
        "O usuário não está desativado.",
        ""
      ];

      header("Location: {$_ENV['HOST_BASE']}usuarios");
      exit;
    }
  }

  /** Ativa o usuário e manda um email de confirmação para criar uma nova senha. */
  public function ativarUsuario():void
  {
    $ativar = $this->repo->ativar($this->data["usuario_id"]);

    if ($ativar){
      $_SESSION["alerta"] = [
        "Usuário ativado com sucesso!",
        "Ele deverá criar uma nova senha."
      ];

      // Tenta enviar o email de confirmação para criar nova senha
      $this->emailConfirmacao(
        $this->data["usuario_id"],
        $this->data["usuario_nome"],
        $this->data["usuario_email"]
      );
    } else {
      $_SESSION["alerta"] = [
        "O usuário não foi ativado.",
        "Tente novamente mais tarde."
      ];
    }
    header("Location: {$_ENV['HOST_BASE']}usuarios");
    exit;
  }
}