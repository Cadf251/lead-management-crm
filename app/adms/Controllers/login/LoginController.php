<?php

namespace App\adms\Controllers\login;

use App\adms\Core\AppContainer;
use App\adms\Core\OperationResult;
use App\adms\Helpers\CSRFHelper;

class LoginController extends LoginBase
{
  public function index()
  {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
      $result = $this->formSubmit("form_login",
        fn(array $data) => $this->getService()->verifyLogin($data)
      );

      if ($result->hadSucceded()) {
        $result->redirect("dashboard");
      } else {
        $result->setCsrfToken(CSRFHelper::generateCSRFToken("form_login"));
      }

      // Monta a resposta
      echo json_encode($result->getForAjax());
      exit;
    }

    if (AppContainer::getAuthUser()->isLoggedIn()) {
      $this->redirect("dashboard");
    }

    $this->loadViewLogin();
  }

  public function logout()
  {
    AppContainer::getAuthUser()->logout();
  }

  public function forgotPass()
  {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
      $result = $this->formSubmit(
        "forgot_pass",
        fn(array $data) => $this->getService()->forgotPass($data)
      );

      $result->report();
      $result->redirect("login");

      echo json_encode($result->getForAjax());
      exit;
    }

    $this->loadViewLogin("Esqueci a senha", "forgot-pass");
  }

  public function createPass(?string $param)
  {
    if (empty($param) || $param === null) {
      $this->invalidToken();
    }

    $parts = explode("-", $param);
    $serverId = (int)$parts[0];
    $token = $parts[1];

    // token is valid?
    $token = $this->getService()->validateToken((int)$serverId, $token);

    if ($token === null) {
      $this->invalidToken();
    }

    // post
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
      $result = $this->formSubmit(
        "form_confirmar",
        fn(array $data) => $this->getService()->createPass((int)$serverId, $data, $token)
      );

      if ($result->hadSucceded()) {
        $result->redirect("dashboard");
      }

      echo \json_encode($result->getForAjax());
      exit;
    }

    // LoadView "confirmar" if not failed
    $this->loadViewLogin("Nova senha", "create-pass");
  }

  private function invalidToken()
  {
    $result = new OperationResult();
    $result->warning("TOKEN inválido.");
    $result->report();
    $this->redirect();
  }

  protected function renderCard(object $entity): string
  {
    return "";
  }
}