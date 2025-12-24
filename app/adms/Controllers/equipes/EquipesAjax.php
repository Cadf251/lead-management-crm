<?php

namespace App\adms\Controllers\equipes;

use App\adms\Helpers\CreateOptions;
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
          $result = $this->alterarRecebimento((int)$_POST["equipe_id"], (int)$_POST["usuario_id"], (int)$_POST["set"]);
          break;
        case "priorizar":
          $result = $this->priorizar((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
          break;
        case "prejudicar":
          $result = $this->prejudicar((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
          break;
        case "remover-usuario":
          $result = $this->removerUsuario((int)$_POST["equipe_id"], (int)$_POST["usuario_id"]);
          break;
        default:
          $this->falha(
            "A task não é permitida",
            ["task" => $task, "POST" => $_POST]
          );
          break;
      }
      echo json_encode(["sucesso" => $result, "html" => $this->renderizarCard((int)$_POST["equipe_id"])]);
      exit;
    }
  }

  /**
   * Altera se o usuário pode receber leads ou não
   * 
   * @param int $equipeId o ID da Equipe
   * @param int $usuarioId o ID do usuario
   * @param int $set 0 para não e 1 para sim
   * 
   * @return bool
   */
  public function alterarRecebimento(int $equipeId, int $usuarioId, int $set):bool {
    return $this->repo->alterarRecebimento($equipeId, $usuarioId, $set);
  }

  /**
   * Prioriza a vez do usuario em +1
   * 
   * @param int $equipeId o ID da Equipe
   * @param int $usuarioId o ID do usuario
   * 
   * @return bool
   */
  public function priorizar(int $equipeId, int $usuarioId):bool {
    return $this->repo->priorizar($equipeId, $usuarioId);
  }

  /**
   * Prejudica a vez do usuario em -1
   * 
   * @param int $equipeId o ID da Equipe
   * @param int $usuarioId o ID do usuario
   * 
   * @return bool
   */
  public function prejudicar(int $equipeId, int $usuarioId):bool {
    return $this->repo->prejudicar($equipeId, $usuarioId);
  }

  public function removerUsuario(int $equipeId, int $usuarioId):bool
  {
    return $this->repo->retirarUsuario($equipeId, $usuarioId);
  }

  /**
   * Faz a mágica acontencer. Renderiza o card final para a resposta do JSON
   */
  public function renderizarCard(int $equipeId){
    $equipe = $this->repo->selecionarEquipe($equipeId);
    $this->data["funcoes"] = $this->repo->selecionarOpcoes("equipes_usuarios_funcoes");
    return require "./app/adms/Views/equipes/partials/equipe-card.php";
  }

  public function falha(string $message, array $array)
  {
    echo json_encode(["sucesso" => false]);
    GenerateLog::generateLog("error", $message, $array);
    exit;
  }
}