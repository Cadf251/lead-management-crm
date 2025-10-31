<?php

namespace App\adms\Controllers\leads;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Repositories\LeadsRepository;
use App\adms\Models\Services\DbConnectionClient;

abstract class LeadAbstract
{
  public int $id;
  public string $nome;
  public string $email;
  public string $celular;
  public array $utm;
  public int $statusId;

  public LeadsRepository $repo;

  public function __construct(array|null $credenciais)
  {
    $conexao = new DbConnectionClient($credenciais);
    $this->repo = new LeadsRepository($conexao->conexao);
  }
}
