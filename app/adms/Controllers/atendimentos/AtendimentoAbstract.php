<?php

namespace App\adms\Controllers\atendimentos;

use App\adms\Models\Repositories\AtendimentosRepository;
use App\adms\Models\Services\DbConnectionClient;

abstract class AtendimentoAbstract
{
  public int $id;
  public int $equipeId;
  public int $usuarioId;
  public int $leadId;
  public int $statusId;
  public string $usuarioNome;
  public string $usuarioEmail;
  public string $usuarioCelular;

  public AtendimentosRepository $repo;

  public function __construct(array|null $credenciais = null)
  {
    $conexao = new DbConnectionClient($credenciais);
    $this->repo = new AtendimentosRepository($conexao->conexao);
  }
}
