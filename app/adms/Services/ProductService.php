<?php

namespace App\adms\Services;

use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\sales\Product;
use App\adms\Repositories\ProductRepository;
use Exception;
use Generator;
use PDO;

class ProductService
{
  private ProductRepository $repository;
  private OperationResult $result;

  public function __construct(PDO $conn)
  {
    $this->repository = new ProductRepository($conn);
    $this->result = new OperationResult;
  }

  public function create(string $name, ?string $description = null): OperationResult
  {
    try {
      $product = Product::new($name, $description);

      $this->repository->create($product);

      $this->result->addMessage("Produto criado com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível criar o produto.");
    }

    return $this->result;
  }

  public function edit(Product $product, array $data)
  {
    try {
      $product->setName($data["name"] ?? $product->getName());
      $product->setDescription($data["description"] ?? null);
      $this->repository->save($product);
      $this->result->addMessage("Produto alterado com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível alterar o produto."); 
    }

    return $this->result;
  }

  public function delete(Product $product)
  {
    try {
      // Parece redundante usar o objeto em vez do ID puro, mas o objeto deve ser validado para ser deletado.
      $this->repository->delete($product->getId());
      $this->result->addMessage("Produto deletado com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível deletar o produto."); 
    }

    return $this->result;
  }
}