<?php

namespace App\adms\Database;

use App\adms\Helpers\ErrorHandler;
use PDO;
use PDOException;

class DbConnectionGlobal
{
  public $conexao = null;

  /**
   * Constrói as credenciais da conexão PDO para evitar o trabalho de repassa-los eternamente, e já conecta ao MySQL
   * Além disso, contém helpes para executar SQL com efetividade
   * 
   * @param string $conexao Conexão com o PDO
   * 
   * @return void Não há necessidade de retornar nada
   */
  public function __construct()
  {
    try {
      $this->conexao = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};charset=utf8mb4;dbname={$_ENV['DB_NAME']}",
        $_ENV["DB_USER"],
        $_ENV["DB_PASS"]
      );
    } catch (PDOException $e) {
      ErrorHandler::deadEndError("001. Erro ao conectar com o servidor", "Algo deu errado com a conexão do servidor.", "critical", "Erro ao conectar com o banco de dados",
      ["PDOException" => $e->getMessage()]);
      die();
    }
  }
}