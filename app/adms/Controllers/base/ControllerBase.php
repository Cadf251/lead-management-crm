<?php

namespace App\adms\Controllers\base;

use App\adms\Core\LoadView;
use App\adms\Core\OperationResult;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;

abstract class ControllerBase
{
  protected array $data = [];
  protected string $viewFolder;
  protected string $defaultView;
  protected string $redirectPath;

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

  protected function renderPartial(string $file, array $presented = [])
  {
    extract($presented);
    return require APP_ROOT . "app/adms/Views/{$this->viewFolder}/partials/{$file}.php";
  }

  protected function formView(
    string $folder,
    string $file,
    array $extraData = [],
  ) {
    $this->data = array_merge($this->data, $extraData);

    // 1. Retorna uma View
    $content = require APP_ROOT . "app/adms/Views/$folder/$file.php";

    $result = new OperationResult();
    $result->setOverlay($content);
    
    echo json_encode($result->getForAjax());
    exit;
  }

  protected function formSubmit(
    string $csrfKey,
    callable $action,
  ): OperationResult {
    $form = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // 1. Tenta Processar o POST
    if (CSRFHelper::validateCSRFToken($csrfKey, $form['csrf_token'])) {

      /** @return OperationResult $result */
      return $action($form);
    }


    $result = new OperationResult();
    $result->failed("Algo deu errado");
    return $result;
  }

  protected function processCreateSubmit(
    OperationResult $result,
    string $instanceKey
  ) {
    if ($result->hadSucceded()) {
      $html = $this->renderCard($result->getInstance($instanceKey));
      $result->setAppend(".js--main", $html);
      $result->closeOverlay();
    }

    echo json_encode($result->getForAjax());
    exit;
  }

  protected function processEditSubmit(
    OperationResult $result,
    string $instanceKey
  ) {
    if ($result->hadSucceded()) {
      $object = $result->getInstance($instanceKey);
      $html = $this->renderCard($object);
      $result->setChange(".card--{$object->getId()}", $html);
      $result->closeOverlay();
    }

    echo json_encode($result->getForAjax());
    exit;
  }

  protected function identifyOr404(string $objectId, object $repository, string $message = "Registro desconhecido."): ?object
  {
    $object = $repository->select((int)$objectId);

    if ($object === null) {
      $result = new OperationResult();
      $result->failed($message);
      echo json_encode($result->getForAjax());
      exit;
    }

    return $object;
  }

  protected function isPost(): bool
  {
    return $_SERVER['REQUEST_METHOD'] === "POST";
  }

  abstract protected function renderCard(object $entity): string;
}
