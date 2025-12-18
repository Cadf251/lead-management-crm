<?php

namespace App\adms\Controllers\leads;

use App\adms\Helpers\GenerateLog;
use App\adms\Repositories\LeadsRepository;
use App\adms\Database\DbConnectionClient;

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
