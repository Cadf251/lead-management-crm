<?php

namespace App\adms\Controllers\atendimentos;

use App\adms\Helpers\GenerateLog;
use Exception;


/**
 * @deprecated
 */
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
   * @return array|Exception As informações do atendimento
   */
  public function novoAtendimento():array|Exception
  {
    if ((!isset($this->equipeId) || (!isset($this->leadId))))
      throw new Exception("Equipe ID ou Lead ID não está setado.");

    // Próximo usuário
    $usuario = $this->repo->pegarProximoUsuario($this->equipeId);

    if ($usuario === false)
      throw new Exception("Não foi possível selecionar um usuário nessa equipe.");

    // Incrementa a vez do próximo usuário
    $incrementar = $this->repo->incrementarUsuario((int)$usuario["eu_id"]);

    if ($incrementar === false)
      throw new Exception("Não foi possível incrementar a vez do usuário");

    $atendimentoId = $this->repo->novoAtendimento($usuario["usuario_id"], $this->equipeId, $this->leadId);
    
    if (($atendimentoId === false) || ($atendimentoId === 0))
      throw new Exception("Não foi possível gerar o atendimento.");

    return [
      "atendimento_id" => $atendimentoId,
      "usuario_id" => (int)$usuario["usuario_id"],
      "usuario_nome" => $usuario["nome"],
      "usuario_email" => $usuario["email"],
      "usuario_celular" => $usuario["celular"]
    ];
  }
}