<?php

namespace App\adms\Database;

use App\adms\Core\AppContainer;
use App\adms\Helpers\ErrorHandler;
use App\adms\Services\AuthUser;
use PDO;
use PDOException;

/**
 * Conecta dinâmicamente com o servidor do cliente.
 * 
 * Para fazer a conexão com o banco de dados usando as credenciais da $_SESSION, passe null como parâmetro.
 * Em casos extraordinários, passe as credenciais para conexão.
 * 
 * @param array $credenciais Um array de credenciais ou null; Se null, utilizará a $_SESSION["db_credenciais"]
 * @param array $credenciais Formato do array: [
 *  string "host", strig "db_name", string "user", string "pass"
 * ]
 */
class DbConnectionClient
{
  public ?PDO $conexao = null;
  private string $host;
  private string $db_name;
  private string $user;
  private string $pass;

  public function __construct(array|null $credenciais)
  {
    $array = $credenciais ?? AppContainer::getAuthUser()->getCredenciais();

    $host   = $array['host'];
    $db_name = $array['db_name'];
    $user   = $array['user'];
    $pass   = $array['pass'];

    try {
      $this->conexao = new PDO(
        "mysql:host=$host;port={$_ENV['DB_PORT']};charset=utf8mb4;dbname=$db_name",
        $user,
        $pass
      );
    } catch (PDOException $e) {
      ErrorHandler::deadEndError(
        "001. Erro ao conectar com o servidor",
        "Algo deu errado com a conexão do servidor.",
        "critical",
        "Erro ao conectar com o banco de dados",
        ["PDOException" => $e->getMessage()]
      );
      die();
    }
  }
}
