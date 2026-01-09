<?php

namespace App\adms\Controllers\produtos;

use App\adms\Helpers\CSRFHelper;

class CriarProduto extends ProdutosAbstract
{
  public function index(): void
  {
    // Verifica o form
    $this->data["form"] = $_POST;

    if (
        isset($this->data["form"]["csrf_token"])
        && CSRFHelper::validateCSRFToken(
          "form_produto",
          $this->data["form"]["csrf_token"])
    ) {
      $data = $this->data["form"];

      $result = $this->service->create(
        $data["name"],
        $data["description"]
      );

      $result->report();

      $this->redirect();
    }

    $content = require APP_ROOT."app/adms/Views/produtos/criar-produto.php";

    echo json_encode([
      "sucess" => true,
      "html" => $content
    ]);
  }
}