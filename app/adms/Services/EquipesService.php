<?php

namespace App\adms\Services;

use App\adms\Core\OperationResult;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\Colaborador;
use App\adms\Repositories\EquipesRepository;
use Exception;
use PDO;

class EquipesService
{
  private ?PDO $conexao;
  private EquipesRepository $repo;
  private OperationResult $result;

  public function __construct(PDO $conexao)
  {
    $this->conexao = $conexao;
    $this->repo = new EquipesRepository($this->conexao);
    $this->result = new OperationResult();
  }

  public function criar(string $nome, ?string $descricao = null): ?OperationResult
  {
    try {
      $equipe = Equipe::novo($nome, $descricao);

      $this->repo->criarEquipe($equipe);
      $this->result->addMessage("A equipe foi criada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR);
      $this->result->failed("Não foi possível criar a equipe.");
    }
    return $this->result;
  }

  public function ativar(Equipe $equipe): ?OperationResult
  {
    try {
      $equipe->ativar();
      $this->repo->salvar($equipe);
      $this->result->addMessage("A equipe {$equipe->getNome()} foi ativada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi ativada.");
    }
    return $this->result;
  }

  public function pausar(Equipe $equipe): ?OperationResult
  {
    try {
      $equipe->pausar();
      $this->repo->salvar($equipe);
      $this->result->addMessage("A equipe {$equipe->getNome()} foi pausada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi pausada.");
    }
    return $this->result;
  }

  public function desativar(Equipe $equipe): ?OperationResult
  {
    try {
      $equipe->desativar();
      $this->repo->salvar($equipe);
      $this->result->addMessage("A equipe {$equipe->getNome()} foi desativada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi desativada.");
    }
    return $this->result;
  }

  public function editar(Equipe $equipe, array $dados): ?OperationResult
  {
    try {
      $equipe->setNome($dados["nome"]);
      $equipe->setDescricao($dados["descricao"]);
      $this->repo->salvar($equipe);
      $this->result->addMessage("A equipe foi editada com sucesso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["equipe" => $equipe]);
      $this->result->failed("A equipe não foi editada.");
    }
    return $this->result;
  }

  public function novoColaborador(Equipe $equipe, array $dados): ?OperationResult
  {
    if ($equipe === null) {
      $this->result->failed("Essa equipe não foi localizada.");
      return $this->result;
    }

    $partes = explode(",", $dados["usuario_id"]);

    $usuarioId = (int)trim($partes[0]);
    // $nivelId = (int)trim($partes[1]);

    try {
      // Não confie no valor informado pelo usuário no form
      // O remédio para um psicopata é um psicopata e meio
      $nivelId = $this->repo->getNivel((int)$usuarioId);

      if ($nivelId === null) {
        $this->result->failed("Algo ocorreu errado");
        return $this->result;
      }
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["post" => $dados]);
      $this->result->failed("Algo ocorreu errado");
      return $this->result;
    }

    $funcaoId = (int)$dados["funcao_id"];

    $podeReceberLeads = (bool)$dados["pode_receber_leads"];

    try {
      $colaborador = new Colaborador();
      $colaborador->setUsuarioId($usuarioId);
      $colaborador->setRecebeLeads($podeReceberLeads);
      $colaborador->setNivelId($nivelId);

      // Verifica se pode ser gerente, senão força a ser colaborador
      if (($funcaoId === 2) && !$colaborador->podeSerGerente()) {
        $funcaoId = 1;
      }

      $colaborador->setFuncao($funcaoId);
      $colaborador->setVez($equipe->getVezMinima());

      $this->repo->criarColaborador($equipe, $colaborador);
      $this->result->addMessage("Usuário adicionado com sucesso à equipe.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, ["post" => $dados]);
      $this->result->failed("A equipe não foi editada.");
    }

    return $this->result;
  }

  public function alterarFuncao(Colaborador $colaborador, int $funcaoId)
  {
    try {
      $funcao = $this->repo->getFuncao($funcaoId);

      $colaborador->setFuncao(
        $funcao["id"],
        $funcao["nome"],
        $funcao["descricao"]
      );

      $this->repo->salvarColaborador($colaborador);
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

  public function alterarRecebimentoDeLeads(Equipe $equipe, Colaborador $colaborador, bool $set)
  {
    try {
      $colaborador->setRecebeLeads($set);

      if ($set === false) {
        $colaborador->setVez(0);
      } else {
        $colaborador->setVez(
          $equipe->getVezMinima()
        );
      }

      $this->repo->salvarColaborador($colaborador);
      $this->result->addMessage("Sucesso silêncioso.");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
        "set" => $set
      ]);
      $this->result->failed("Não foi possível mudar o recebimento de leads.");
    }

    return $this->result;
  }

  public function prejudicar(Colaborador $colaborador):OperationResult
  {
    try {
      $colaborador->incrementarVez();
      $this->repo->salvarColaborador($colaborador);
      $this->result->addMessage("");
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível alterar a vez do usuário.");
    }
    return $this->result;
  }

  public function priorizar(Colaborador $colaborador):OperationResult
  {
    try {
      $colaborador->diminuirVez();
      $this->repo->salvarColaborador($colaborador);
      $this->result->addMessage("");
    } catch (Exception $e){
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível alterar a vez do usuário.");
    }
    return $this->result;
  }

  public function removerColaborador(Colaborador $colaborador):OperationResult
  {
    try {
      $this->repo->removerColaborador($colaborador);
      $this->result->addMessage("");
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ERROR, [
        "colaborador" => $colaborador,
      ]);
      $this->result->failed("Não foi possível remover esse usuário.");
    }

    return $this->result;
  }
}
