<?php

namespace App\adms\Services;

use App\adms\Core\AppContainer;
use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\teams\Team;
use App\adms\Models\teams\TeamUser;
use App\adms\Repositories\TeamsRepository;
use App\adms\Repositories\TeamUserRepository;
use Exception;
use PDO;

class TeamsService
{
  private ?PDO $conexao;
  private TeamsRepository $repo;
  private TeamUserRepository $userRepo;
  private OperationResult $result;

  public function __construct(?PDO $conexao = null)
  {
    $this->conexao = $conexao ?? AppContainer::getClientConn();
    $this->repo = new TeamsRepository($this->conexao);
    $this->userRepo = new TeamUserRepository($this->conexao);
    $this->result = new OperationResult();
  }

  /**
   * Lista de forma completa as equipes com seus respectivos usuários
   */
  public function list(): array
  {
    try {
      $final = [];
      $teams = $this->repo->list();

      /** @var Team $team */
      foreach ($teams as $team) {
        $users = $this->userRepo->list($team->getId());        
        $team->setUsers($users);
        $final[] = $team;
      }
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
    }

    return $final;
  }

  public function select(int $teamId): ?Team
  {
    try {
      $team = $this->repo->select($teamId);

      if ($team === null) return null;
      $users = $this->userRepo->list($team->getId());
      $team->setUsers($users);
      return $team;
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      return null;
    }
  }

  public function create(string $nome, ?string $descricao = null): ?OperationResult
  {
    try {
      $equipe = Team::new($nome, $descricao);

      $this->repo->create($equipe);

      $this->result->saveInstance("team", $equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível criar a equipe.");
    }
    return $this->result;
  }

  public function activate(Team $equipe): ?OperationResult
  {
    try {
      $equipe->activate();
      $this->repo->save($equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi ativada.");
    }
    return $this->result;
  }

  public function pause(Team $equipe): ?OperationResult
  {
    try {
      $equipe->pause();
      $this->repo->save($equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi pausada.");
    }
    return $this->result;
  }

  public function disable(Team $equipe): ?OperationResult
  {
    try {
      $equipe->disable();
      $this->repo->save($equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi desativada.");
    }
    return $this->result;
  }

  public function edit(Team $equipe, array $dados): ?OperationResult
  {
    try {
      $equipe->setName($dados["nome"]);
      $equipe->setDescription($dados["descricao"]);
      $this->repo->save($equipe);
      $this->result->saveInstance("team", $equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi editada.");
    }
    return $this->result;
  }

  public function newTemUser(Team $equipe, array $dados): ?OperationResult
  {
    if ($equipe === null) {
      $this->result->failed("Essa equipe não foi localizada.");
      return $this->result;
    }

    $usuarioId = (int)$dados["usuario_id"];

    try {
      $data = $this->userRepo->getUserInfo((int)$usuarioId);

      if ($data === null) {
        $this->result->failed("Algo ocorreu errado");
        return $this->result;
      }
      $nivelId = $data["nivel_acesso_id"];
      $userName = $data["nome"];
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["post" => $dados]);
      $this->result->failed("Algo ocorreu errado");
      return $this->result;
    }

    $funcaoId = (int)$dados["funcao_id"];

    $podeReceberLeads = (bool)$dados["pode_receber_leads"];

    try {
      $colaborador = new TeamUser();
      $colaborador->setUserId($usuarioId);
      $colaborador->setReceiveLeads($podeReceberLeads);
      $colaborador->setLevelId($nivelId);

      $colaborador->setFunction($funcaoId);
      $colaborador->setUserName($userName);
      $colaborador->setTime($equipe->getMinTime());

      $this->userRepo->create($equipe, $colaborador);
      $equipe->setOneUser($colaborador);
      $this->result->saveInstance("user", $colaborador);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["post" => $dados]);
      $this->result->failed("A equipe não foi editada.");
    }

    return $this->result;
  }

  public function changeFunction(TeamUser $colaborador, int $funcaoId)
  {
    try {

      $colaborador->setFunction($funcaoId);

      $this->userRepo->save($colaborador);
      $this->result->addMessage("Função alterada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
        "funcao" => $funcaoId
      ]);
      $this->result->failed("Não foi possível alterar a função do usuário na equipe.");
    }

    return $this->result;
  }

  public function changeReceivingLeads(Team $equipe, TeamUser $colaborador, bool $set)
  {
    try {
      $colaborador->setReceiveLeads($set);

      if ($set === false) {
        $colaborador->setTime(0);
      } else {
        $colaborador->setTime(
          $equipe->getMinTime()
        );
      }

      $this->userRepo->save($colaborador);

      // Reset updated user
      $equipe->removeUser($colaborador->getId());
      $equipe->setOneUser($colaborador);

      // Save Instances
      $this->result->saveInstance("team", $equipe);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
        "set" => $set
      ]);
      $this->result->failed("Não foi possível mudar o recebimento de leads.");
    }

    return $this->result;
  }

  public function harm(Team $team, TeamUser $colaborador):OperationResult
  {
    try {
      $colaborador->increaseTime();
      $this->userRepo->save($colaborador);

      // Reset updated user
      $team->removeUser($colaborador->getId());
      $team->setOneUser($colaborador);
      $this->result->saveInstance("team", $team);
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível alterar a vez do usuário.");
    }
    return $this->result;
  }

  public function prioritize(Team $team, TeamUser $colaborador):OperationResult
  {
    try {
      $colaborador->dimishTime();
      $this->userRepo->save($colaborador);

      // Reset updated user
      $team->removeUser($colaborador->getId());
      $team->setOneUser($colaborador);
      $this->result->saveInstance("team", $team);
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível alterar a vez do usuário.");
    }
    return $this->result;
  }

  public function deleteUser(Team $team, TeamUser $colaborador):OperationResult
  {
    try {
      $this->userRepo->delete($colaborador);
      
      $team->removeUser($colaborador->getId());

      $this->result->saveInstance("team", $team);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível remover esse usuário.");
    }

    return $this->result;
  }
}
