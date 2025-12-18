<?php

namespace App\adms\Database;

use App\adms\Helpers\GenerateLog;
use PDO;
use PDOException;

/**
 * Faz operações no banco de dados usando a conxão recebida
 * Classe abstrata, serve apenas para ser herdada.
 * 
 * @param $conexao Recebe uma coneão com o banco de dados
 */
abstract class DbOperations
{
  /**
   * Recebe uma conexão com o PDO
   */
  public function __construct(private $conexao){}

  /**
   * Única função responsável por executar o SQL.
   * Nunca executar o SQL em outro ambiente, sempre use está função para centralização.
   * 
   * @param string $query A query SQL
   * @param array $params Os parametros a serem injetados na query por bindParam
   * @param bool $toArray Se true, retorna um array com os fetchAll
   * 
   * @return array|bool Os dados do fetchAll, ou se falhou ou obteve sucesso.
   */
  public function executeSQL(string $query, array $params = [], bool $toArray = true) : array|bool
  {
    $prepare = $this->conexao->prepare($query);

    foreach ($params as $key => $valor){
      $tipo = $this->tratarTipo($valor);

      if (strpos($query, $key) !== false){
        $prepare->bindValue($key, $valor, $tipo);
      }
    }

    try {
      $prepare->execute();
    } catch (PDOException $e){
      GenerateLog::generateLog("error", "A execução do SQL falhou", ["query" => $query, "params" => $params, "erro" => $e->getMessage()]);
      return false;
    }

    if ($toArray)
      return $prepare->fetchAll(PDO::FETCH_ASSOC);
    else
      return true;
  }

  /**
   * Recebe um valor e verifica o tipo de dado para retornar o
   * tipo de filtro PDO que será aplicado no bindParam.
   * 
   * @param mixed $valor O valor a ser avaliado
   * 
   * @return PDO::PARAM_TIPO
   */
  private function tratarTipo($valor) : int
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

  /**
   * Seleciona as opções de uma tabela para usar em select>options em formulários
   * 
   * @param string $tabela A tabela que será feita a consulta
   * @param int|null $id A linha que virá como selecionada
   * 
   * @return array|bool HTML
   */
  public function selecionarOpcoes(string $tabela) :array|bool
  {
    $query = <<<SQL
      SELECT id,nome
      FROM $tabela
    SQL;

    return $this->executeSQL($query);
  }

  /**
   * Insere valores no banco de dados
   * 
   * Preste atenção no formato do array $params.
   * 
   * @param string $table A tabela a ser inserido
   * @param array $params Os parâmetros a serem inseridos. O formato deve ser: [[":nome_campo" => "valor"], [...]]. O nome do campo deve ser literalmente o do campo do banco de dados.
   * 
   * @return int|false O ID do campo registrado ou false se falhar
   */
  function insertSQL(string $table, array $params):int|false
  {
    $placeholders = array_keys($params);
    $fields = [];
    
    foreach ($params as $key => $_){
      $fields[] = str_replace(":", "", $key);
    }

    $implodedFields = implode(", ", $fields);
    $implodedPlaceholders = implode(", ", $placeholders);

    $query = <<<SQL
      INSERT INTO $table ({$implodedFields})
      VALUES ({$implodedPlaceholders})
    SQL;

    $result = $this->executeSQL($query, $params, false);
    if ($result)
      return $this->conexao->lastInsertId();
    else
      return false;
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
   * @return bool
   */
  function updateSQL(string $tabela, array $params, int $id):bool
  {
    $set = [];
    foreach ($params as $key => $_){
      $field = str_replace(":", "", $key);
      $set[] = "$field = $key";
    }

    $implodedSet = implode(",\n", $set);

    $query = <<<SQL
      UPDATE $tabela
      SET
        $implodedSet
      WHERE
        id = :id
    SQL;

    $params[":id"] = $id;
    GenerateLog::generateLog("info", "query", [$query]);
    return $this->executeSQL($query, $params, false);
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
  public function deleteSQL(string $tabela, array $where, array $params):bool
  {
    $whereSQL = implode("\n  AND", $where);
    $query = <<<SQL
    DELETE FROM {$tabela}
    WHERE {$where}
    SQL;

    return $this->executeSQL($query, $params, false);
  }

  /**
   * Deleta registros do SQL com um ID
   * 
   * @param string $tabela A tabela alvo
   * @param int $id O ID que deve ser excluído
   * 
   * @return bool
   */
  public function deleteByIdSQL(string $tabela, int $id):bool
  {
    $query = <<<SQL
    DELETE FROM {$tabela}
    WHERE id = :this_id
    SQL;

    $params = [
      ":this_id" => $id
    ];

    return $this->executeSQL($query, $params, false);
  }

  /**
   * Verifica se um registro existe
   * 
   * @param string $tabela A tabela a ser procurada
   * @param string $where A condição da busca em SQL
   * @param array $params Os parâmetros do Where para evitar injection
   * @param bool $returnId Manipula o return. Se for true, retorna o ID do registro caso ele exista
   * 
   * @return bool|int true|int se existir, false se não existir.
   */
  public function existe(string $tabela, string $where, array $params = [], bool $returnId = false){
    $query = <<<SQL
      SELECT id
      FROM {$tabela}
      WHERE
        {$where}
      LIMIT 1
    SQL;

    $result = $this->executeSQL($query, $params);

    if (empty($result))
      return false;
    else if ($returnId)
      return $result[0]["id"];
    else
      return true;
  }
}