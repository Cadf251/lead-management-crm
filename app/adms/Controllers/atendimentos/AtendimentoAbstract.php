<?php

namespace App\adms\Controllers\atendimentos;

use App\adms\Core\LoadView;
use App\adms\Repositories\AtendimentosRepository;
use App\adms\Database\DbConnectionClient;

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


  protected array $data = [
    "title" => "Em Atendimento"
  ];

  public AtendimentosRepository $repo;

  public function __construct(array|null $credenciais = null)
  {
    $conexao = new DbConnectionClient($credenciais);
    $this->repo = new AtendimentosRepository($conexao->conexao);
  }

  public function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  protected function render(string $viewName = "em-atendimento"): void
  {
    $loadView = new LoadView("adms/Views/atendimentos/$viewName", $this->data);
    $loadView->loadView();
  }

  protected function redirect($to = "em-atendimento"): void
  {
    header("Location: {$_ENV['HOST_BASE']}{$to}");
    exit;
  }
}
