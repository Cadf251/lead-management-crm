<?php

namespace App\adms\Controllers\base;

use App\adms\Core\AppContainer;
use App\adms\Core\LoadView;
use PDO;

abstract class ControllerBase
{
  protected array $data = [];
  protected string $viewFolder;
  protected string $defaultView;
  protected string $redirectPath;

  public function __construct()
  {
    $conexao = AppContainer::getClientConn();

    // O filho decide quais classes instanciar
    $this->boot($conexao);
  }

  /**
   * Método abstrato: Cada filho deve dizer como inicia seu repo e service
   */
  abstract protected function boot(PDO $conexao): void;

  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  protected function render(?string $viewName = null): void
  {
    $name = $viewName ?? $this->defaultView;

    // Padronizamos o caminho usando a pasta definida pelo filho
    $path = "adms/Views/{$this->viewFolder}/$name";
    $loadView = new LoadView($path, $this->data);
    $loadView->loadView();
  }

  public function redirect(?string $to = null): void
  {
    $dest = $to ?? $this->redirectPath;
    header("Location: {$_ENV['HOST_BASE']}{$dest}");
    exit;
  }

  protected function renderPartial(string $file, array $params = [])
  {
    extract($params);
    return require APP_ROOT . "app/adms/Views/{$this->viewFolder}/partials/{$file}.php";
  }

  /**
   * Atalho para validar CSRF
   */
  protected function validateCSRF(string $key): bool
  {
    $token = $_POST['csrf_token'] ?? null;
    return $token && \App\adms\Helpers\CSRFHelper::validateCSRFToken($key, $token);
  }
}
