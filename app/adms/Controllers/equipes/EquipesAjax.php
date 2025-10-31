<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\GenerateLog;

class EquipesAjax extends EquipesAbstract
{
  public function index(null|string $task)
  {
    if (($task === null) || ($task === "")){
      $this->falha(
        "Uma requisição de AJAX não recebeu uma task pela URL",
        ["task" => $task, 'POST' => $_POST]
      );
    } else {
      switch($task){
        case "alterar-recebimento":
          $this->alterarRecebimento((int)$_POST["equipe_id"], (int)$_POST["usuario_id"], (int)$_POST["set"]);
        case "priorizar":
          $this->priorizar((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
        case "prejudicar":
          $this->prejudicar((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
        case "remover-usuario":
          $this->removerUsuario((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
        default:
          $this->falha(
            "A task não é permitida",
            ["task" => $task, "POST" => $_POST]
          );
      }
      exit;
    }
  }

  public function alterarRecebimento(int $equipeId, int $usuarioId, int $set)
  {
    $result = $this->repo->alterarRecebimento($equipeId, $usuarioId, $set);

    echo json_encode(["sucesso" => $result]);
    GenerateLog::generateLog("info", "O recebimento foi alterado.", []);
    exit;
  }

  public function priorizar(int $equipeId, int $usuarioId)
  {
    $result = $this->repo->priorizar($equipeId, $usuarioId);

    echo json_encode(["sucesso" => $result]);
    exit;
  }

  public function prejudicar(int $equipeId, int $usuarioId)
  {
    $result = $this->repo->prejudicar($equipeId, $usuarioId);

    echo json_encode(["sucesso" => $result]);
    exit;
  }

  public function removerUsuario(int $equipeId, int $usuarioId)
  {
    $result = $this->repo->retirarUsuario($equipeId, $usuarioId);

    echo json_encode(["sucesso" => $result]);
    exit;
  }

  public function falha(string $message, array $array)
  {
    echo json_encode(["sucesso" => false]);
    GenerateLog::generateLog("error", $message, $array);
    exit;
  }
}