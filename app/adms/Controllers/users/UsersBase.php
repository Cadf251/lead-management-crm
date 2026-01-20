<?php

namespace App\adms\Controllers\users;

use App\adms\Controllers\base\ControllerBase;
use App\adms\Core\AppContainer;
use App\adms\Models\users\User;
use App\adms\Models\Usuario;
use App\adms\Presenters\UsuarioPresenter;
use App\adms\Repositories\UsersRepository;
use App\adms\Services\UsersService;

abstract class UsersBase extends ControllerBase
{
  protected string $viewFolder = "usuarios";
  protected string $redirectPath = "usuarios";
  protected string $defaultView = "listar-usuarios";

  protected array $data = ["title" => "Usuários"];
  private ?UsersService $service = null;
  private ?UsersRepository $repo = null;

  protected function getService(): UsersService
  {
    if ($this->service === null) {
      $this->service = new UsersService();
    }

    return $this->service;
  }

  protected function getRepository(): UsersRepository
  {
    if ($this->repo === null) {
      $this->repo = new UsersRepository(AppContainer::getClientConn());
    }

    return $this->repo;
  }

  protected function renderCard(object $usuario): string
  {
    return $this->renderPartial(
      "usuario-card",
      ["user" => UsuarioPresenter::present([$usuario])[0]]
    );
  }
}
