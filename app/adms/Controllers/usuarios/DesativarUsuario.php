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
      $result->failed("Esse usuário não existe.");
      $result->report();
      echo json_encode(["sucesso" => false]);
      exit;
    }

    $result = $this->service->desativar($usuario);
  
    echo json_encode([
      ...$result->getForAjax(),
      "html" => $this->renderizarCard($usuario)]);
    exit;
  }
}