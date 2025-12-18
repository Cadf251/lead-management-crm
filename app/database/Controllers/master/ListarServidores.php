<?php

namespace App\database\Controllers\master;

use App\adms\Database\DbConnectionGlobal;
use App\adms\Core\LoadView;
use App\database\Models\DatabaseRepository;

class ListarServidores
{
  private array|null|string $data;

  public function index():void
  {
    $conexao = new DbConnectionGlobal();
    
    // Recupera os dados de clientes
    $repo = new DatabaseRepository($conexao->conexao);
    
    $this->data = [
      "servidores" => $repo->listarClientes(),
      "title" => "Visualizar clientes"
    ];

    // Carrega a VIEW
    $loadView = new LoadView("database/Views/master/clientes", $this->data);
    $loadView->loadViewMaster();
  }
}