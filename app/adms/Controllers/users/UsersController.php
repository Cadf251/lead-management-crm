<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\users\UsersBase;
use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\NivelSistema;
use App\adms\Models\SystemLevel;
use App\adms\Models\users\User;
use App\adms\Presenters\UsuarioPresenter;

class UsersController extends UsersBase
{
  /**
   * Listar
   */
  public function index(): void
  {
    $this->list();
  }

  public function list(): void
  {
    $users = $this->getRepository()->list();

    $this->setData([
      "usuarios" => UsuarioPresenter::present($users)
    ]);

    // Carregar a VIEW
    $this->render();
  }

  public function create(): void
  {
    if ($this->isPost()) {
      $result = $this->formSubmit(
        "form_usuario",
        action: fn(array $data): OperationResult => $this->getService()->create(
          $data["nome"],
          $data["email"],
          $data["celular"],
          $data["nivel_acesso_id"]
        )
      );

      $this->processCreateSubmit(
        result: $result,
        instanceKey: "user"
      );
    }

    $this->formView(
      "usuarios",
      "criar-usuario",
      extraData: [
        "usuarios" => null,
        "form-options" => SystemLevel::getSelectOptions()
      ]
    );
  }

  public function edit(string $userId)
  {
    $user = $this->identify($userId);

    if ($this->isPost()) {
      $result = $this->formSubmit(
        "form_usuario",
        action: fn(array $data): OperationResult => $this->getService()->edit($user, $data)
      );

      $this->processEditSubmit(
        $result,
        "user"
      );
    }

    $this->formView(
      "usuarios",
      "editar-usuario",
      extraData: [
        "usuarios" => UsuarioPresenter::present([$user]),
        "form-options" => SystemLevel::getSelectOptions($user->getSystemLevelId())
      ]
    );
  }

  private function identify(string $userId): ?User
  {
    return $this->identifyOr404($userId, $this->getRepository(), "Usuário desconhecido.");
  }

  /**
   * Fluxo de vida principal de mudança de status de users
   * 
   * @return void AJAX
   */
  private function main(string $userId, callable $action): void
  {
    $user = $this->identify($userId);

    /** @var OperationResult $result */
    $result = $action($user);

    $result->setUpdate(".card--{$user->getId()}", $this->renderCard($user));

    echo json_encode($result->getForAjax());
    exit;
  }

  public function disable(string $userId): void
  {
    $this->main($userId, function (User $user): OperationResult {
      return $this->getService()->disable($user);
    });
  }

  public function resetPassword(string $userId): void
  {
    $this->main($userId, function (User $user): OperationResult {
      return $this->getService()->resetPassword($user);
    });
  }

  public function reactivate(string $userId): void
  {
    $this->main($userId, function (User $user): OperationResult {
      return $this->getService()->reactivate($user);
    });
  }

  public function resendMail(string $userId): void
  {
    $this->main($userId, function (User $user): OperationResult {
      return $this->getService()->resendMail($user);
    });
  }
}
