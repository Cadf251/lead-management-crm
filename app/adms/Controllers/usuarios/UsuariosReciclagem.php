<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Core\OperationResult;
use App\adms\Models\Usuario;

/** 
 * ✅ FUNCIONAL - CUMPRE V1
 * Classe abstrata que cuida do ciclo de vida de um usuário.
 */
abstract class UsuariosReciclagem extends UsuariosAbstract
{
  abstract protected function executar(Usuario $usuario) :OperationResult;

  public function main(string|int|null $usuarioId)
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

    $result = $this->executar($usuario);

    echo json_encode([
      ...$result->getForAjax(),
      "html" => $this->renderizarCard($usuario)]);
    exit;
  }
}