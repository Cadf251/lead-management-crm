<?php

namespace App\adms\Controllers\teams;

use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\teams\Team;
use App\adms\Presenters\EquipePresenter;

class TeamsController extends TeamsBase
{
  public function index(): void
  {
    $this->list();
  }

  public function list(): void
  {
    $teams = $this->getService()->list();
    
    $this->setData([
      "equipes" => EquipePresenter::present($teams)
    ]);

    $this->render();
  }

  public function create(): void
  {
    if($this->isPost()) {
      $result = $this->formSubmit(
        "form_equipe",
        fn(array $data) => $this->getService()->create(
          $data["nome"],
          $data["descricao"] ?? null
        )
      );

      $this->processCreateSubmit(
        result: $result,
        instanceKey: "team",
      );
    }

    $this->formView(
      $this->viewFolder,
      "criar-equipe",
      extraData: [
        "equipe" => null
      ]
    );
  }

  public function edit(string $teamId): void 
  {
    $team = $this->identify($teamId);

    if ($this->isPost()) {
      $result = $this->formSubmit(
        "form_equipe",
        fn(array $data) => $this->getService()->edit($team, $data)
      );

      $this->processEditSubmit(
        result: $result,
        instanceKey: "team"
      );
    }

    $this->formView(
      $this->viewFolder,
      "editar-equipe",
      extraData: [
        "equipe" => EquipePresenter::present([$team])
      ]
    );
  }

  private function main(string $teamId, callable $action): void
  {
    $team = $this->identify($teamId);

    /** @var OperationResult $result */
    $result = $action($team);

    $result->setChange(".card--{$team->getId()}", $this->renderCard($team));

    echo json_encode($result->getForAjax());
    exit;
  }

  public function activate(string $teamId)
  {
    $this->main($teamId, function(Team $team): OperationResult {
      return $this->getService()->activate($team);
    });
  }

  public function pause(string $teamId)
  {
    $this->main($teamId, function(Team $team): OperationResult {
      return $this->getService()->pause($team);
    });
  }

  public function disable(string $teamId)
  {
    $team = $this->identify($teamId);

    /** @var OperationResult $result */
    $result = $this->getService()->disable($team);

    $result->setRemove(".card--{$team->getId()}");

    echo json_encode($result->getForAjax());
    exit;
  }

  private function identify(string $teamId): ?Team
  {
    $team = $this->getService()->select($teamId);

    if ($team === null) {
      $result = new OperationResult();
      $result->failed("Equipe não encontrada");
      echo json_encode($result->getForAjax());
      exit;
    }

    return $team;
  }
}