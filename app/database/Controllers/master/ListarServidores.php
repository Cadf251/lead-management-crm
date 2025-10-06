<?php

namespace App\database\Controllers\master;

use App\adms\Models\Services\DbConnectionGlobal;
use App\adms\Views\Services\LoadViewService;
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
    $loadView = new LoadViewService("database/Views/master/clientes", $this->data);
    $loadView->loadViewMaster();
  }
}