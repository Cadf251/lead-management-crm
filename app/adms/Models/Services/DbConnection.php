<?php

namespace App\adms\Models\Services;

use App\adms\Helpers\GenerateLog;
use PDO;
use PDOException;

abstract class DbConnection
{
  private $conexao = null;

  /**
   * Constrói as credenciais da conexão PDO para evitar o trabalho de repassa-los eternamente, e já conecta ao MySQL
   * 
   * Além disso, contém helpes para executar SQL com efetividade
   * 
   * @param string $conexao Conexão com o PDO
   * 
   * @return void Não há necessidade de retornar nada
   */
  public function __construct()
  {
    $this->getConnection();
  }

  private function getConnection() :void
  {
    try {
      $this->conexao = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};charset=utf8mb4;dbname={$_ENV['DB_NAME']}",
        $_ENV["DB_USER"],
        $_ENV["DB_PASS"]
      );
      echo "Conexão realizada com sucesso";
    } catch (PDOException $e) {
      GenerateLog::generateLog("critical", "Erro ao conectar com o banco de dados", ["PDOException" => $e->getMessage()]);
      die("Não foi possível conectar ao servidor");
    }
  }

  public function executeSQL(string $query, array $params = [], bool $toArray = true) : array|bool
  {
    $prepare = $this->conexao->prepare($query);

    foreach ($params as $key => $valor){
      $tipo = $this->tratarTipo($valor);

      if (strpos($query, $key) !== false){
        $prepare->bindValue($key, $valor, $tipo);
      }
    }

    $execute = $prepare->execute();

    // SE FALHOU
    if (!$execute)
      return false;

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