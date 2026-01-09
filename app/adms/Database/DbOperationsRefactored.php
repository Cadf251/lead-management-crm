<?php

namespace App\adms\Database;

use App\adms\Helpers\GenerateLog;
use DateTime;
use Exception;
use PDO;
use PDOException;

/**
 * Faz operações no banco de dados usando a conxão recebida
 * 
 * @param $conexao Recebe uma coneão com o banco de dados
 */
class DbOperationsRefactored
{
  /**
   * @param PDO $conexao
   */
  public function __construct(private PDO $conexao) {}

  /**
   * Única função responsável por executar o SQL. Nunca executar o SQL em outro ambiente, sempre use está função para centralização.
   * 
   * @param string $query A query SQL
   * @param array $params Os parametros a serem injetados na query por bindParam
   * @param bool $toArray Se true, retorna um array com os fetchAll
   * 
   * @throws Exception
   * 
   * @return ?array Os dados do fetchAll ou null
   */
  public function execute(string $query, array $params = [], bool $toArray = true): ?array
  {
    try {
      $prepare = $this->conexao->prepare($query);
    } catch (PDOException $e) {
      throw new Exception($e->getMessage(), 0, $e);
    }

    foreach ($params as $key => $valor) {
      if ($valor instanceof DateTime) {
        $valor = $valor->format($_ENV["DATE_FORMAT"] ?? "Y-m-d H:i:s");
      }

      $tipo = $this->tratarTipo($valor);

      if (strpos($query, $key) !== false) {
        // Verifica se a chave já tem o placeholder :
        if (strpos($key, ":") !== 0) {
          $key = ":$key";
        }

        $prepare->bindValue($key, $valor, $tipo);
      }
    }

    try {
      $prepare->execute();
    } catch (PDOException $e) {
      throw new Exception($e->getMessage(), 0, $e);
    }

    if ($toArray) {
      return $prepare->fetchAll(PDO::FETCH_ASSOC);
    } else {
      return null;
    }
  }

  /**
   * Recebe um valor e verifica o tipo de dado para retornar o
   * tipo de filtro PDO que será aplicado no bindParam.
   * 
   * @param mixed $valor O valor a ser avaliado
   * 
   * @return PDO::PARAM_TIPO
   */
  private function tratarTipo($valor): int
  {
    if (is_int($valor))
      return PDO::PARAM_INT;
    else if ($valor === null)
      return PDO::PARAM_NULL;
    else if (is_bool($valor))
      return PDO::PARAM_BOOL;
    else
      return PDO::PARAM_STR;
  }

  private function buildSet(array $params): string
  {
    $set = [];

    foreach ($params as $key => $_) {
      if (strpos($key, ":") === 0) {
        $field = str_replace(":", "", $key);
        $set[] = "$field = $key";
      } else {
        $field = $key;
        $set[] = "$field = :$key";
      }
    }

    return implode(",\n", $set);
  }

  private function buildWhere(array $filters): string
  {
    if (empty($filters)) {
      throw new Exception("WHERE mal formado");
    }

    $conditions = [];

    foreach ($filters as $filter) {
      // Ex: ['status', '=', 'ativo'] ou ['nome', 'LIKE']
      $field = $filter[0];
      $operator = $filter[1] ?? '=';
      $value = $filter[2] ?? $field;

      if (strpos($value, ":") !== 0) {
        $value = ":$value";
      }

      $conditions[] = "($field $operator $value)";
    }

    return implode(" AND ", $conditions);
  }

  /**
   * Faz o fluxo padrão do select
   */
  public function selectOne(
    string $query,
    callable $hydrator,
    array $params = [],
  ) :?object {
    try {
      $result = $this->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return null;
    }

    return $hydrator($result[0]);
  }

  /**
   * Faz o fluxo padrão do select
   */
  public function selectMultiple(
    string $query,
    callable $hydrator,
    array $params = [],
  ) :?array {
    try {
      $result = $this->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return null;
    }

    $final = [];

    foreach ($result as $row) {
      $final[] = $hydrator($row);
    }

    return $final;
  }

  /**
   * Insere valores no banco de dados
   * 
   * Preste atenção no formato do array $params.
   * 
   * @param string $table A tabela a ser inserido
   * @param array $params Os parâmetros a serem inseridos. O formato deve ser: [[":nome_campo" => "valor"], [...]]. O nome do campo deve ser literalmente o do campo do banco de dados.
   * 
   * @throws Exception
   * 
   * @return int
   */
  public function insert(string $table, array $params): int
  {
    $placeholders = [];
    $fields = [];

    foreach ($params as $key => $_) {
      if (strpos($key, ":") === 0) {
        $placeholders[] = $key;
        $fields[] = str_replace(":", "", $key);
      } else {
        $placeholders[] = ":$key";
        $fields[] = $key;
      }
    }

    $implodedFields = implode(", ", $fields);
    $implodedPlaceholders = implode(", ", $placeholders);

    $query = <<<SQL
      INSERT INTO $table ({$implodedFields})
      VALUES ({$implodedPlaceholders})
    SQL;

    try {
      $this->execute($query, $params, false);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    return $this->conexao->lastInsertId();
  }

  /**
   * Faz um UPDATE no banco de dados em um registro com o WHERE personalizado
   * 
   * Monta os SETs dinâmicamente usando o $params.
   * 
   * @param string $tabela A tabela a ser atualizada
   * @param array $params Os parâmetros da operação, com campos literais.
   * @param array $where O where
   * 
   * @return void
   */
  public function update(string $tabela, array $params, array $where): void
  {
    try {
      $query = <<<SQL
        UPDATE $tabela
        SET
          {$this->buildSet($params)}
        WHERE
          {$this->buildWhere($where)}
      SQL;

      $this->execute($query, $params, false);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Faz um UPDATE no banco de dados em um registro com o ID informado
   * 
   * Monta os SETs dinâmicamente usando o $params.
   * 
   * @param string $tabela A tabela a ser atualizada
   * @param array $params Os parâmetros da operação, com campos literais.
   * @param int $id O id que será atualizado
   * 
   * @return void
   */
  public function updateById(string $tabela, array $params, int $id): void
  {
    $query = <<<SQL
      UPDATE $tabela
      SET
        {$this->buildSet($params)}
      WHERE
        id = :id
    SQL;

    $params["id"] = $id;

    try {
      $this->execute($query, $params, false);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Deleta registros de SQL com um WHERE flexível
   * 
   * @param string $tabela A tabela alvo
   * @param array $where As condições WHERE
   * @param array $params Os parâmetros do WHERE
   * 
   * @return bool
   */
  public function delete(string $tabela, array $where, array $params): void
  {
    $whereSQL = implode("\n  AND", $where);
    $query = <<<SQL
    DELETE FROM {$tabela}
    WHERE
      {$this->buildWhere($where)}
    SQL;

    $this->execute($query, $params, false);
  }

  /**
   * Deleta registros do SQL com um ID
   * 
   * @param string $tabela A tabela alvo
   * @param int $id O ID que deve ser excluído
   * 
   * @throws Exception
   * 
   * @return void
   */
  public function deleteById(string $tabela, int $id): void
  {
    $query = <<<SQL
    DELETE FROM {$tabela}
    WHERE id = :this_id
    SQL;

    $params = [
      "this_id" => $id
    ];

    try {
      $this->execute($query, $params, false);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Verifica se um registro existe
   * 
   * @param string $tabela A tabela a ser procurada
   * @param array $where A condição da busca em SQL
   * @param array $params Os parâmetros do Where para evitar injection
   * @param bool $returnId Manipula o return. Se for true, retorna o ID do registro caso ele exista
   * 
   * @throws Exception
   * 
   * @return bool|int true|int se existir, false se não existir.
   */
  public function existe(string $tabela, array $where, array $params = [], bool $returnId = false)
  {
    $query = <<<SQL
      SELECT id
      FROM {$tabela}
      WHERE
        {$this->buildWhere($where)}
      LIMIT 1
    SQL;

    try {
      $result = $this->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) {
      return false;
    } else if ($returnId) {
      return $result[0]["id"];
    } else {
      return true;
    }
  }

  /**
   * Seleciona as opções de uma tabela para usar em select>options em formulários
   * 
   * @param string $tabela A tabela que será feita a consulta
   * @param int|null $id A linha que virá como selecionada
   * 
   * @throws Exception
   * 
   * @return array
   */
  public function selecionarOpcoes(string $tabela): array
  {
    $query = <<<SQL
      SELECT id,nome
      FROM $tabela
    SQL;

    try {
      return $this->execute($query);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
