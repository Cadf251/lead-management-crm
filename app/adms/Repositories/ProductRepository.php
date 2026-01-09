<?php

namespace App\adms\Repositories;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\sales\Product;
use Exception;

class ProductRepository extends RepositoryBase
{
  private string $table = "produtos";

  public function queryBase(): string
  {
    return <<<SQL
    SELECT id, nome, descricao
    FROM {$this->table}
    SQL;
  }

  /**
   * @return ?array<Product>
   */
  public function list(): ?array
  {
    try {
      return $this->sql->selectMultiple(
        $this->queryBase(),
        fn(array $row) => $this->hydrate($row)
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function select(int $id): ?Product
  {
    try {
      return $this->sql->selectOne(
        $this->queryBase() . "\nWHERE id = :id",
        fn(array $row) => $this->hydrate($row),
        ["id" => $id]
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  private function hydrate(array $row): Product
  {
    return new Product(
      $row["id"],
      $row["nome"],
      $row["descricao"]
    );
  }

  public function create(Product $product): int
  {
    $params = [
      "nome" => $product->getName(),
      "descricao" => $product->getDescription()
    ];

    try {
      return $this->sql->insert($this->table, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function save(Product $product): void
  {
    $params = [
      "nome" => $product->getName(),
      "descricao" => $product->getDescription()
    ];

    try {
      $this->sql->updateById($this->table, $params, $product->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }  
  }

  public function delete(int $id): void
  {
    try {
      $this->sql->deleteById($this->table, $id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
