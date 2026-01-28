<?php

namespace App\adms\Controllers\teams;

use App\adms\Core\OperationResult;
use App\adms\Helpers\CreateOptions;
use App\adms\Models\teams\Team;
use App\adms\Models\teams\TeamUser;
use App\adms\Models\teams\TeamUserFunction;
use App\adms\Presenters\EquipePresenter;

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
      $result->setUpdate(".js--number-badge", $team->countUsers());
      $result->setAppend("tbody", $this->renderTeamUser(
        $team,
        $result->getInstance("user")
      ));
      $result->closeOverlay();

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

  public function remove(string $teamUserId): void
  {
    $team = $this->identifyOrFailJson($_POST["equipe_id"]);

    $teamUser = $this->identifyUserInTeamOrFailJson($team, $teamUserId);

    $result = $this->getService()->deleteUser($team, $teamUser);

    $this->addInfoBoxToResponse($result);

    $this->addNumberBadgeToResponse($result);

    echo json_encode($result->getForAjax());
    exit;
  }

  public function changeFunction(string $teamUserId): void
  {
    $teamUser = $this->getUserRepository()->select((int)$teamUserId);

    $set = (int)$_POST["funcao_id"];

    $result = $this->getService()->changeFunction($teamUser, $set);

    echo \json_encode($result->getForAjax());
    exit;
  }

  public function changeReceiving(string $teamUserId): void
  {
    $team = $this->identifyOrFailJson($_POST["equipe_id"] ?? null);

    $teamUser = $this->identifyUserInTeamOrFailJson($team, $teamUserId);

    $set = $_POST["recebe_leads"];
    if ($set === "true") $set = true;
    else if ($set === "false") $set = false;
    else {
      $result = new OperationResult();
      $result->failed("Algo deu errado.");
      echo json_encode($result->getForAjax());
      exit;
    }

    $result = $this->getService()->changeReceivingLeads($team, $teamUser, $set);

    $this->addInfoBoxToResponse($result);

    echo json_encode($result->getForAjax());
    exit;
  }

  public function changeTime(string $teamUserId): void
  {
    $team = $this->identifyOrFailJson($_POST["equipe_id"]);

    $teamUser = $this->identifyUserInTeamOrFailJson($team, $teamUserId);

    $set = $_POST["set"];

    if ($set === "harm") $result = $this->getService()->harm($team, $teamUser);
    else if ($set === "prioritize") $result = $this->getService()->prioritize($team, $teamUser);
    else {
      $result = new OperationResult();
      $result->failed("Algo deu errado");
      echo json_encode($result->getForAjax());
      exit;
    }

    $this->addInfoBoxToResponse($result);

    echo json_encode($result->getForAjax());
    exit;
  }

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

  private function identifyOrFailJson(?string $teamId): ?Team
  {
    $result = new OperationResult();

    if ($teamId === null || $teamId === "") {
      $result->failed("Equipe inválida");
      echo json_encode($result->getForAjax());
      exit;
    }

    $team = $this->getService()->select((int)$teamId);
    
    if ($team === null) {
      $result->failed("Equipe inválida");
      echo json_encode($result->getForAjax());
      exit;
    }

    return $team;
  }

  private function identifyUserInTeamOrFailJson(Team $team, string $teamUserId): ?TeamUser
  {
    $teamUser = $team->getUserById((int)$teamUserId);

    if ($teamUser === null) {
      $result = new OperationResult();
      $result->failed("Usuário inválido");
      echo json_encode($result->getForAjax());
      exit;
    }

    return $teamUser;
  }

  private function addInfoBoxToResponse(OperationResult $result): void
  {
    $team = $result->getInstance("team");

    $html = $this->renderInfoBox($team);

    $result->setCustomParam("info_box_html", $html);
  }

  private function addNumberBadgeToResponse(OperationResult $result): void
  {
    /** @var Team $team */
    $team = $result->getInstance("team");

    $html = $team->countUsers();

    $result->setCustomParam("number_badge_html", $html);
  }

  private function renderTeamUser(Team $team, TeamUser $teamUser)
  {
    $presented = EquipePresenter::presentOneColaborador($team, $teamUser);

    return <<<HTML
    <tr>
      <td>{$presented["usuario_nome"]}</td>
      <td>{$presented["funcao_select"]}</td>
      <td class="cell-centered">{$presented["recebe_leads_switch"]}</td>
      <td class="cell-centered">{$presented["vez_buttons"]}</td>
      <td class="cell-centered">{$presented["remover_button"]}</td>
    </tr>
    HTML;
  }

  private function renderInfoBox(Team $team)
  {
    return EquipePresenter::fila($team)["infobox"]->render();
  }
}
