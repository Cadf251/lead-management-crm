<?php

namespace App\adms\Repositories;

/**
 * Esta classe é responsável por interagir com a tabela de usuários de diferentes servidores
 * Ela herda a operação com o banco de dados e fornece métodos de encontrar o usuário com base no email e servidor.
 * 
 * Por herdar a DbOperations, ela pede uma conexão com o PDO no __contruct
 */
class LoginRepository extends RepositoryBase
{
  /**
   * Verifica se o servidor existe
   * 
   * @param int $servidorId
   * 
   * @return bool|array False se não existir, um array simplificado com as credenciais se existir.
   */
  public function verificarServidor(int $servidorId) :bool|array
  {    
    $query = <<<SQL
    SELECT
      host, user, pass, db_name 
    FROM servidores 
    WHERE 
      id = :servidor_id
      AND `status` = 1
    LIMIT 1
    SQL;

    $params = [
      ":servidor_id" => $servidorId
    ];

    $execute = $this->sql->execute($query, $params);

    if (empty($execute) OR ($execute === false))
      return false;
    else
      return $execute[0];
  }
}