<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Core\OperationResult;
use App\adms\Models\Usuario;

class ResetarSenha extends UsuariosReciclagem
{
  public function index(string|null $usuarioId):void {
    // Chama o fluxo principal
    $this->main($usuarioId);
  }

  public function executar(Usuario $usuario): OperationResult
  {
    return $this->service->resetarSenha($usuario);
  }
}