<?php

namespace App\adms\Controllers\teams;

use App\adms\Core\OperationResult;
use App\adms\Helpers\CreateOptions;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\teams\Team;
use App\adms\Models\teams\TeamUserFunction;
use App\adms\Presenters\EquipePresenter;
use Exception;

class TeamUsersController extends TeamsBase
{
  public function index(string $teamId): void
  {
    $this->list($teamId);
  }

  public function list(string $teamId): void 
  {
    $team = $this->identifyOrRedirect($teamId);

    $this->setData([
      "title" => "Listar Colaboradores",
      "equipes" => EquipePresenter::present([$team]),
    ]);

    $this->render("listar-colaboradores");
  }

  public function add(string $teamId): void
  {
    $team = $this->getService()->select((int)$teamId);
    $users = $this->getUserRepository()->listAbleForTeam($team);

    if ($this->isPost()) {
      $result = $this->formSubmit(
        "add_usuario",
        fn(array $data) => $this->getService()->newTemUser($team, $data)
      );

      $result->setUpdate(".js--infobox", $this->renderInfoBox($team));

      echo json_encode($result->getForAjax());
      exit;
    }

    if ($users === null) {
      $result = new OperationResult();
      $result->failed("Nenhum usuário habilitado para essa equipe.");
      echo json_encode($result->getForAjax());
      exit;
    }

    $this->formView(
      $this->viewFolder,
      "novo-colaborador",
      [
        "usuarios" => EquipePresenter::presentNovoColaborador($users),
        "funcoes" => CreateOptions::criarOpcoes(TeamUserFunction::getSelectOptions()),
        "equipe_id" => $teamId
      ]
    );
  }

  public function remove(string $teamId): void {}

  public function changeFunction(string $teamId): void {}

  public function changeReceiving(string $teamId): void {}

  public function changeTime(string $teamId): void {}

  private function identifyOrRedirect(string $teamId): ?Team
  {
    $team = $this->getService()->select((int)$teamId);

    if ($team === null) {
      $result = new OperationResult();
      $result->failed("Equipe não encontrada.");
      $result->report();
      $this->redirect();
    }

    return $team;
  }

  private function renderInfoBox(Team $team)
  {
    return EquipePresenter::fila($team)["infobox"];
  }
}
