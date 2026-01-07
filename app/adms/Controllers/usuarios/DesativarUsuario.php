<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Core\OperationResult;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class DesativarUsuario extends UsuariosAbstract
{
  public function index(string|null $usuarioId):void
  {
    // Instancia o usuário
    $usuario = $this->repo->selecionar((int)$usuarioId);

    if($usuario === null){
      $result = new OperationResult();
      $result->falha("Esse usuário não existe.");
      $_SESSION["alerta"] = $result->getAlerta();
      echo json_encode(["sucesso" => false]);
      exit;
    }

    $result = $this->service->desativar($usuario);
  
    echo json_encode([
      "sucesso" => $result->sucesso(),
      "alerta" => $result->getStatus(),
      "mensagens" => $result->getMensagens(),
      "html" => $this->renderizarCard($usuario)]);
    exit;
  }
}