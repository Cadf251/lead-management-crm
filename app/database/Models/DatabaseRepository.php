<?php

namespace App\database\Models;

use App\adms\Database\DbConnectionClient;
use App\adms\Helpers\GenerateLog;
use App\adms\Database\DbOperations;
use App\adms\Database\DbOperationsRefactored;
use App\api\Models\ApiClient;
use Exception;
use PDO;

class DatabaseRepository extends DbOperations
{
  private DbOperationsRefactored $sql;

  public function __construct($conexao)
  {
    $this->sql = new DbOperationsRefactored($conexao);
  }
  
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
    SELECT email_master, host, user, pass, `db_name`
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

  public function selectClientByApiToken(string $token): ?ApiClient
  {
    $query = <<<SQL
    SELECT id, email_master, host, user, pass, `db_name`, api_token
    FROM servidores
    WHERE api_token = :token
    SQL;

    $params = [
      "token" => $token
    ];

    try {
      $result = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return null;
    }

    return $this->hydrateClient($result[0]);
  }

  private function hydrateClient(array $row): ?ApiClient
  {
    try {
      $conn = new DbConnectionClient($row);
    } catch (Exception $e) {
      GenerateLog::log($e, GenerateLog::ALERT, []);
      return null;
    }

    return new ApiClient(
      $row["id"],
      $row["email_master"],
      $row["api_token"],
      $conn->conexao
    );
  }
}
