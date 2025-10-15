<?php

namespace App\adms\Controllers\equipes;

use App\adms\Models\Repositories\EquipesRepository;
use App\adms\Models\Services\DbConnectionClient;

abstract class EquipesAbstract
{
  /** @var protected $repo O repositório de usuários  */
  protected $repo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de equipes */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new EquipesRepository($conn->conexao);
  }
}