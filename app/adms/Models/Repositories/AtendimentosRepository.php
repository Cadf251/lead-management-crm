<?php

namespace App\adms\Models\Repositories;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbOperations;

class AtendimentosRepository extends DbOperations
{
  private string $tabela = "atendimentos";

  public function recuperarAtendimento(int $leadId, int $equipeId)
  {
    $query = <<<SQL
    SELECT a.id atendimento_id, a.usuario_id, a.created atendimento_created,
    u.nome, u.email, u.celular
    FROM atendimentos a
    INNER JOIN usuarios u ON u.id = a.usuario_id
    WHERE 
      (lead_id = :lead_id)
      AND (equipe_id = :equipe_id)
    ORDER BY a.created DESC
    LIMIT 1
    SQL;

    $params = [
      ":lead_id" => $leadId,
      ":equipe_id" => $equipeId
    ];

    return $this->executeSQL($query, $params);
  }

  public function pegarProximoUsuario(int $equipeId):array|false
  {
    $query = <<<SQL
    SELECT 
      eu.id AS eu_id, eu.usuario_id,
      u.nome, u.email, u.celular
    FROM equipes_usuarios AS eu
    INNER JOIN usuarios AS u ON eu.usuario_id = u.id
    WHERE equipe_id = :equipe_id
      AND pode_receber_leads = 1
      AND vez = (
        SELECT MIN(vez)
        FROM equipes_usuarios
        WHERE equipe_id = :equipe_id
        AND pode_receber_leads = 1
      )
    ORDER BY eu.id ASC
    LIMIT 1
    SQL;

    $params = [
      ":equipe_id" => $equipeId
    ];

    $result = $this->executeSQL($query, $params);

    if (empty($result) || ($result === false))
      return false;
    else
      return $result[0];
  }

  public function incrementarUsuario(int $equipeUsuarioId)
  {
    $query = <<<SQL
    UPDATE equipes_usuarios
    SET vez = vez + 1
    WHERE id = :id
    LIMIT 1
    SQL;

    $params = [
      ":id" => $equipeUsuarioId
    ];

    return $this->executeSQL($query, $params, false);
  }

  /**
   * Cria um novo atendimento e retorna o seu ID.
   */
  public function novoAtendimento(int $usuarioId, int $equipeId, int $leadId):int|false
  {
    if (($usuarioId === 0) || ($equipeId === 0) || ($leadId === 0)){
      GenerateLog::generateLog("error", "Um dos parâmetros era 0", [
        "usuario_id" => $usuarioId, 
        "equipe_id" => $equipeId, 
        "lead_id" => $leadId
      ]);
      return false;
    }
    
    $params = [
      ":equipe_id" => $equipeId,
      ":usuario_id" => $usuarioId,
      ":lead_id" => $leadId,
      ":created" => date($_ENV['DATE_FORMAT'])
    ];

    return $this->insertSQL($this->tabela, $params);
  }
}