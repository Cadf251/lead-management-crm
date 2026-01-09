<?php

namespace App\adms\Controllers\produtos;

use App\adms\Core\OperationResult;
use App\adms\Helpers\CSRFHelper;
use App\adms\Presenters\ProductPresenter;

class EditarProduto extends ProdutosAbstract
{
  public function index(int|string|null $productId): void
  {
    $product = $this->repository->select((int)$productId);

    if ($product === null) {
      $result = new OperationResult();
      $result->failed("Produto não encontrado.");
      $result->report();
      $this->redirect();
    }

    $presented = ProductPresenter::present([$product]);

    $this->setData([
      "product" => $presented[0]
    ]);

    // Verifica o form
    $this->data["form"] = $_POST;

    if (
        isset($this->data["form"]["csrf_token"])
        && CSRFHelper::validateCSRFToken(
          "form_produto",
          $this->data["form"]["csrf_token"])
    ) {
      $data = $this->data["form"];

      $result = $this->service->edit($product, $data);

      $result->report();

      $this->redirect();
    }

    $content = require APP_ROOT."app/adms/Views/produtos/editar-produto.php";

    echo json_encode([
      "sucess" => true,
      "html" => $content
    ]);
  }
}