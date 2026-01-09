<?php

namespace App\adms\Controllers\ofertas;

use App\adms\Core\LoadView;
use App\adms\Database\DbConnectionClient;
use App\adms\Repositories\OfferRepository;

abstract class OfertaAbstract
{
  protected array $data = [
    "title" => "Ofertas",
  ];

  public OfferRepository $repository;

  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repository = new OfferRepository($conn->conexao); 
  }

  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  protected function getData(): array
  {
    return $this->data;
  }

  protected function render(string $viewPath = "listar-ofertas"): void
  {
    $loadView = new LoadView("adms/Views/ofertas/$viewPath", $this->getData());
    $loadView->loadView();
  }

  public function redirect(): void
  {
    header("Location: {$_ENV['HOST_BASE']}listar-ofertas");
    exit;
  }
}
