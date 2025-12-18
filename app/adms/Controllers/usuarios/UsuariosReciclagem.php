<?php

namespace App\adms\Controllers\usuarios;

/** Classe abstrata que cuida do ciclo de vida de um usuário. */
abstract class UsuariosReciclagem extends UsuariosAbstract
{
  abstract protected function executar(): bool;
  protected bool $enviarEmail = false;

  public function fluxoPrincipal(string|int|null $usuarioId)
  {
    // Verifica o ID
    if (empty($usuarioId)) {
      $_SESSION["alerta"] = [
        "Erro!",
        "❌ Este usuário não existe ou é inválido."
      ];
      $this->redirect();
    }

    $this->setInfoById((int)$usuarioId);

    // Executa o método principal
    $tryMain = $this->executar();

    if($tryMain === false)
      $this->redirect();

    // Se precisar envia o e-mail de confirmação
    if ($this->enviarEmail === true){
      $mail = $this->emailConfirmacao();

      if ($mail === true){
        if (!isset($_SESSION["alerta"][0])) $_SESSION["alerta"][0] = "Sucesso!";
        $_SESSION["alerta"][1][] = "✅ O e-mail de confirmação de senha foi enviado com sucesso.";
      } else {
        if (!isset($_SESSION["alerta"][0])) $_SESSION["alerta"][0] = "Erro!";
        $_SESSION["alerta"][1][] = "❌ O e-mail de confirmação de senha não foi enviado.";
      }
    }

    // Redireciona
    $this->redirect();
  }
}