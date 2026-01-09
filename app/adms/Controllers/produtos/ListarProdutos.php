<?php

namespace App\adms\Controllers\produtos;

use App\adms\Presenters\ProductPresenter;

class ListarProdutos extends ProdutosAbstract
{
  public function index():void
  {
    $list = $this->repository->list();
    $products = ProductPresenter::present($list);

    $this->setData([
      "products" => $products
    ]);
    
    $this->render();
  }
}