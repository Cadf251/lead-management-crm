<?php

namespace App\adms\Controllers\produtos;

use App\adms\Core\OperationResult;

class DeletarProduto extends ProdutosAbstract
{
  public function index(string|null|int $productId)
  {
    $product = $this->repository->select((int)$productId);

    if ($product === null) {
      $result = new OperationResult();
      $result->failed("Produto não encontrado.");
      echo json_encode($result->getForAjax());
      exit;
    }

    $result = $this->service->delete($product);
    
    echo json_encode($result->getForAjax());
    exit;
  }
}
