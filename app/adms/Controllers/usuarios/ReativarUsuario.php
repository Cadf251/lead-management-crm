<?php

namespace App\adms\Controllers\usuarios;

class AtivarUsuario extends UsuariosAbstract
{
  public function index(string|null $usuarioId):void {
    // Instancia o usuário
    $usuario = $this->repo->selecionar((int)$usuarioId);

    if($usuario === null){
      $_SESSION["alerta"] = [
        "❌ Erro",
        ["Esse usuário não existe."]
      ];
      echo json_encode(["sucesso" => false]);
      exit;
    }

    $result = $this->service->reativar($usuario);
  
    if($result->sucesso() === true){
      $_SESSION["alerta"] = [
        "✅ Sucesso!",
        $result->mensagens()
      ];
    } else {
      $_SESSION["alerta"] = [
        "❌ Erro!",
        $result->mensagens()
      ];
    }

    echo json_encode(["sucesso" => $result->sucesso(), "html" => $this->renderizarCard($usuario)]);
    exit;
  }
}