<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Core\OperationResult;
use App\adms\Models\Usuario;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 */
class ReativarUsuario extends UsuariosReciclagem
{
  public function index(string|null $usuarioId): void
  {
    // Chama o fluxo principal
    $this->main($usuarioId);
  }

  public function executar(Usuario $usuario): OperationResult
  {
    return $this->service->reativar($usuario);
  }
}
