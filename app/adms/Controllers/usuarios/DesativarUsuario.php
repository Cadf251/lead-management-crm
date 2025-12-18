<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\GenerateLog;
use Exception;

class DesativarUsuario extends UsuariosAbstract
{
  public function index(string|null $usuarioId)
  {
    try {
      $usuario = $this->repo->selecionar((int)$usuarioId);

      $usuario->desativar($this->repo);

      $this->tokenRepo->desativarDeUsuario($usuario->id);

      if(isset($usuario->foto)){
        $this->service->apagarFoto($usuario);
      }

      $this->repo->salvar($usuario);

      echo json_encode(["sucesso" => true, "html" => $this->renderizarCard($usuario)]);
      exit;
    } catch (Exception $e){
      GenerateLog::generateLog("error", "Não foi possível desativar um usuário.", [
        "usuario_id" => $usuarioId, "error" => $e->getMessage()
      ]);
      echo json_encode(["sucesso" => false]);
      exit;
    }
  }
}