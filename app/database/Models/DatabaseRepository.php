<?php

namespace App\database\Models;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbOperations;

class DatabaseRepository extends DbOperations
{
  /**
   * Retorna uma query base
   */
  private function queryBase()
  {
    return <<<SQL
    SELECT
      id, nome, host, user, pass, `db_name`, versao, `status`, created, modified
    FROM servidores
    SQL;
  }

  /**
   * Retorna todos os servidores.
   */
  public function listarClientes()
  {
    $query = $this->queryBase();
    return $this->executeSQL($query);
  }

  /**
   * Recupera apenas um servidor
   */
  public function selecionar(int $id)
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE
        id = :id
    SQL;

    $param = [
      ":id" => $id
    ];

    return $this->executeSQL($query, $param)[0];
  }

  /**
   * Ativa um servidor
   * 
   * @param int $servidorId O ID do servidor que será ativado
   */
  public function ativar(int $servidorId)
  {
    $this->updateSQL("servidores", [":status" => 1], $servidorId);
  }

  public function verificarTokenApi(string $token)
  {
    $query = <<<SQL
    SELECT host, user, pass, `db_name`
    FROM servidores
    WHERE api_token = :token
    SQL;

    $params = [
      ":token" => $token
    ];

    $result = $this->executeSQL($query, $params);
    
    if ((empty($result)) || $result === false || $result === null)
      return false;
    else
      return $result[0];
  }
}
