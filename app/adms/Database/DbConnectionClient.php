<?php

namespace App\adms\Database;

use App\adms\Core\AppContainer;
use App\adms\Helpers\ErrorHandler;
use App\adms\Services\AuthUser;
use Exception;
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

  public function __construct(array|null $credenciais = null)
  {
    $array = $credenciais ?? AppContainer::getAuthUser()->getCredentials();

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
      throw new Exception("Não foi possível conectar ao banco $host", 0, $e);
    }
  }
}
