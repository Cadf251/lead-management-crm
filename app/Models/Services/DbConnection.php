<?php

namespace App\Models\Services;

use App\Helpers\GenerateLog;
use PDO;
use PDOException;

abstract class DbConnection
{
  private string $host = "";
  private string $usuario = "";
  private string $senha = "";
  private string $dbname = "";
  private int $porta = 3306;
  private $conexao = null;

  /**
   * Constrói as credenciais da conexão PDO para evitar o trabalho de repassa-los eternamente, e já conecta ao MySQL
   * 
   * @param string $host Servidor MySQL
   * @param string $usuario Usuário MySQL
   * @param string $senha Senha do banco
   * @param string $dbname Nome do banco de dados
   * @param string $porta Porta da Conexão
   * @param string $conexao Conexão com o PDO
   * 
   * @return void Não há necessidade de retornar nada
   */
  public function __construct(string $host, string $usuario, string $senha, string $dbname, int $porta = 3306)
  {
    $this->host = $host;
    $this->usuario = $usuario;
    $this->senha = $senha;
    $this->dbname = $dbname;
    $this->porta = $porta;
    $this->getConnection();
  }

  private function getConnection() :void
  {
    try {
      $this->conexao = new PDO(
        "mysql:host={$this->host};port={$this->porta};charset=utf8mb4;dbname={$this->dbname}",
        $this->usuario,
        $this->senha
      );
      echo "Conexão realizada com sucesso";
    } catch (PDOException $e) {
      GenerateLog::generateLog("critical", "Erro ao conectar com o banco de dados", ["PDOException" => $e->getMessage()]);
      die("Não foi possível conectar ao servidor");
    }
  }
}