<?php

namespace App\database\Controllers\master;

use App\adms\Models\Services\DbConnectionGlobal;
use App\database\Models\DatabaseRepository;

class AtivarServidor
{
  private int $id;

  public function index(string|null|int $id)
  {
    $this->id = (int)$id;
    
    $conexao = new DbConnectionGlobal();
    
    // Recupera os dados de clientes
    $repo = new DatabaseRepository($conexao->conexao);

    $repo->ativar($id);

    header("Location: {$_ENV['HOST_BASE']}listar-servidores");
    exit;
  }
}