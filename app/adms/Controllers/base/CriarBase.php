<?php

namespace App\adms\Controllers\base;

abstract class CriarBase extends ControllerBase
{
  protected string $csrfKey;
  protected string $viewFile;

  public function index(): void
  {
    $this->data["form"] = $_POST;

    // Se houver POST e o token for válido
    if (!empty($_POST) && $this->validateCSRF($this->csrfKey)) {
      $this->handleAction(); // O neto decide como salvar
      $this->redirect();
    }

    // Se chegar aqui, renderiza a view para o AJAX
    $html = $this->captureView($this->viewFile);
    $this->renderAjax($html);
  }

  /**
   * O neto implementará a chamada específica do Service aqui
   */
  abstract protected function handleAction(): void;

  private function captureView(string $file): string
  {
    ob_start();
    // Usamos as variáveis configuradas no filho
    require APP_ROOT . "app/adms/Views/{$this->viewFolder}/{$file}.php";
    return ob_get_clean();
  }

  /**
   * Centraliza a resposta JSON padrão do seu sistema
   */
  protected function renderAjax(string $content): void
  {
    echo json_encode([
      "sucesso" => true,
      "sucess" => true,
      "html" => $content
    ]);
    exit;
  }
}
