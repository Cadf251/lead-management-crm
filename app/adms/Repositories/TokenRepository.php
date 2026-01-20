<?php

namespace App\adms\Repositories;

use App\adms\Models\Token;
use App\adms\Models\Status;
use Exception;
use PDO;

/**
 * ✅ FUNCIONAL - CUMPRE V1
 * 
 * Repositório de Tokens
 */
class TokenRepository extends RepositoryBase
{
  private const TABLE = "tokens";

  private function queryBase()
  {
    $table = self::TABLE;
    return <<<SQL
    SELECT id, token, tipo, contexto, prazo, usuario_id, atendimento_id, token_status_id
    FROM $table
    SQL;
  }

  public function select(string $token): ?Token
  {
    $query = <<<SQL
    {$this->queryBase()}
    WHERE token = :token
    SQL;

    $params = [
      "token" => $token
    ];

    try {
      $array = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception("Erro ao consultar o banco de dados na query: \n $query \n", $e->getCode(), $e);
    }

    if (empty($array)) {
      return null;
    }

    return $this->hydrate($array[0]);
  }

  /**
   * Recupera um TOKEN exato
   */
  public function recover(string $token, string $type, string $context):?Token
  {
    $query = <<<SQL
    {$this->queryBase()}
    WHERE
      token = :token
      AND (tipo = :tipo)
      AND (contexto = :contexto)
    SQL;

    $params = [
      "token" => $token,
      "tipo" => $type,
      "contexto" => $context
    ];

    try {
      $array = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception("Erro ao consultar o banco de dados na query: \n $query \n", $e->getCode(), $e);
    }

    if (empty($array)) {
      return null;
    }

    return $this->hydrate($array[0]);
  }

  private function hydrate(array $row): Token
  {
    $instance = new Token(
      $row["token"],
      $row["tipo"],
      $row["contexto"],
      $row["prazo"] ?? null,
      $row["usuario_id"] ?? null,
      $row["atendimento_id"] ?? null
    );
    $instance->setId($row["id"]);
    $instance->setStatus($row["token_status_id"]);
    return $instance;
  }

  public function create(Token $token)
  {
    $params = [
      "token" => $token->getToken(),
      "tipo" => $token->getType(),
      "contexto" => $token->getContext(),
      "prazo" => $token->getDeadEnd(),
      "atendimento_id" => $token->getSupportId(),
      "usuario_id" => $token->getUserId()
    ];

    try {
      $this->sql->insert(self::TABLE, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function saveStatus(Token $token)
  {
    $params = [
      "token_status_id" => $token->getStatusId()
    ];

    try {
      $this->sql->updateById(self::TABLE, $params, $token->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Desativa todos os TOKENs de um usuário.
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @todo Isso aqui poderia ser um simples trigger no SQL
   */
  public function disableUserTokens(int $usuarioId)
  {
    $query = <<<SQL
      UPDATE tokens
      SET token_status_id = :token_status_id
      WHERE
        usuario_id = :usuario_id
    SQL;

    $params = [
      ":token_status_id" => Status::STATUS_DESATIVADO,
      ":usuario_id" => $usuarioId
    ];

    $this->sql->execute($query, $params, false);
  }
}
