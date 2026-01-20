<?php

namespace App\adms\Controllers\teams;

use App\adms\Controllers\base\ControllerBase;
use App\adms\Core\AppContainer;
use App\adms\Models\teams\Team;
use App\adms\Presenters\EquipePresenter;
use App\adms\Repositories\TeamsRepository;
use App\adms\Repositories\TeamUserRepository;
use App\adms\Services\TeamsService;

abstract class TeamsBase extends ControllerBase
{
  protected string $viewFolder = "equipes";
  protected string $defaultView = "listar-equipes";
  protected string $redirectPath = "equipes";

  protected array $data = ["title" => "Equipes"];

  private ?TeamsService $service = null;
  private ?TeamsRepository $repo = null;
  private ?TeamUserRepository $userRepo = null;

  protected function getService(): TeamsService
  {
    if ($this->service === null) {
      $this->service = new TeamsService();
    }

    return $this->service;
  }

  protected function getRepository(): TeamsRepository
  {
    if ($this->repo === null) {
      $this->repo = new TeamsRepository(AppContainer::getClientConn());
    }

    return $this->repo;
  }

  protected function getUserRepository(): TeamUserRepository
  {
    if ($this->repo === null) {
      $this->userRepo = new TeamUserRepository(AppContainer::getClientConn());
    }

    return $this->userRepo;
  }

  /**
   * @param Team $equipe
   */
  public function renderCard(object $equipe): string
  {
    return $this->renderPartial(
      "equipe-card",
      ["team" => EquipePresenter::present([$equipe])[0]]
    );
  }
}
