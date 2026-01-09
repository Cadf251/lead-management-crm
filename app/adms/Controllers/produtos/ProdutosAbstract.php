<?php

namespace App\adms\Controllers\produtos;

use App\adms\Core\LoadView;
use App\adms\Database\DbConnectionClient;
use App\adms\Repositories\ProductRepository;
use App\adms\Services\ProductService;
use PDO;

abstract class ProdutosAbstract
{
  protected array $data = [
    "title" => "Produtos",
  ];

  private PDO $conn;
  public ProductRepository $repository;
  public ProductService $service;

  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->conn = $conn->conexao;
    $this->repository = new ProductRepository($this->conn); 
    $this->service = new ProductService($this->conn);
  }

  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  protected function getData(): array
  {
    return $this->data;
  }

  protected function render(string $viewPath = "listar-produtos"): void
  {
    $loadView = new LoadView("adms/Views/produtos/$viewPath", $this->getData());
    $loadView->loadView();
  }

  public function redirect(): void
  {
    header("Location: {$_ENV['HOST_BASE']}listar-produtos");
    exit;
  }
}
