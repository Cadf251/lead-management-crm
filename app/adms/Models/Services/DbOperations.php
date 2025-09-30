<?php

namespace App\adms\Models\Services;

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
}