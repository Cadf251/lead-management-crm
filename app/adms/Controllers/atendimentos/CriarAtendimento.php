<?php

namespace App\adms\Controllers\atendimentos;

use App\adms\Helpers\GenerateLog;

class CriarAtendimento extends AtendimentoAbstract
{
  public function index()
  {

  }

  public function atendimentoExiste():array|bool
  {
    $resultado = $this->repo->recuperarAtendimento($this->leadId, $this->equipeId);

    if ((empty($resultado)) || $resultado === false)
      return false;
    else
      return $resultado[0];
  }

  /**
   * Descobre qual o usuário apto para receber o lead e cria um novo atendimento. Incrementa a vez do usuário.
   * 
   * @param $this->equipeId
   * 
   * @return array As informações do atendimento
   */
  public function novoAtendimento():array|false
  {
    if ((!isset($this->equipeId) || (!isset($this->leadId)))){
      GenerateLog::generateLog("error", "Equipe ID ou Lead ID não está setado.", ["equipe_id" => $this->equipeId]);
      return false;
    }

    // Próximo usuário
    $usuario = $this->repo->pegarProximoUsuario($this->equipeId);

    if ($usuario === false){
      GenerateLog::generateLog("error", "Não foi possível selecionar um usuário.", []);
      return false;
    }

    // Incrementa a vez do próximo usuário
    $incrementar = $this->repo->incrementarUsuario((int)$usuario["eu_id"]);

    if ($incrementar === false){
      GenerateLog::generateLog("error", "Não foi possível incrementar a vez do usuário.", ["equipes_usuarios_id" => $usuario["eu_id"]]);
      return false;
    }

    $atendimentoId = $this->repo->novoAtendimento($usuario["usuario_id"], $this->equipeId, $this->leadId);
    
    if (($atendimentoId === false) || ($atendimentoId === 0)){
      GenerateLog::generateLog("error", "Não foi possível criar o atendimento no banco de dados.", []);
      return false;
    }

    return [
      "atendimento_id" => $atendimentoId,
      "usuario_id" => (int)$usuario["usuario_id"],
      "usuario_nome" => $usuario["nome"],
      "usuario_email" => $usuario["email"],
      "usuario_celular" => $usuario["celular"]
    ];
  }
}