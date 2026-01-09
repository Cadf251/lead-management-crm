<?php

namespace App\adms\Models\sales;

use App\adms\Models\Status;
use App\adms\Models\traits\ComumObject;
use App\adms\Models\traits\StatusHandler;

class Offer
{
  use ComumObject;
  use StatusHandler;
  
  private Status $status;
  private ?Product $product;

  public function setProduct(
    int $id,
    string $nome,
    ?string $descricao = null
  )
  {
    $this->product = new Product(
      $id, $nome, $descricao
    );
  }

  public function getProductId(): int
  {
    return $this->product->getId();
  }
}