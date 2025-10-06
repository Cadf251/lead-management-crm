<?php

namespace App\adms\Controllers\usuarios;

class RecuperarSenha extends UsuariosAbstract
{
  /** @var array|string|null $dados Recebe os dados que devem ser enviados para VIEW */
  private array|string|null $data = null;

  /** Recupera os dados para resetar a senha */
  public function index(string|int|null $usuarioId)
  {
    // Seta o ID
    $this->data["usuario_id"] = (int)$usuarioId;

    // Recupera as informações do usuário
    $usuarios = $this->repo->selecionar($this->data["usuario_id"]);
    $usuario = $usuarios[0];
    $this->data["usuario_nome"] = $usuario["u_nome"];
    $this->data["usuario_email"] = $usuario["u_email"];

    $this->resetarSenha(
      $this->data["usuario_id"],
      $this->data["usuario_nome"],
      $this->data["usuario_email"]
    );
  }

  /**
   * Envia o email de confirmação, apaga a senha do e retorna ao status de aguardando confirmação
   */
  public function resetarSenha($usuarioId, $usuarioNome, $usuarioEmail):void
  {
    $reset = $this->repo->resetarSenha($usuarioId);

    if ($reset){
      // Tenta enviar o email de confirmação para criar nova senha
      $result = $this->emailConfirmacao(
        $usuarioId,
        $usuarioNome,
        $usuarioEmail
      );
      if ($result){
        $_SESSION["alerta"] = [
          "E-mail de confirmação de senha enviado!",
          "Peça ao usuário verificar."
        ];
      } else {
        $_SESSION["alerta"] = [
          "O e-mail de troca de senha não foi enviado!",
          "Tente novamente mais tarde."
        ];
      }
    } else {
      $_SESSION["alerta"] = [
        "Algo deu errado!",
        "Tente novamente mais tarde."
      ];
    }
    header("Location: {$_ENV['HOST_BASE']}usuarios");
    exit;
  }
}