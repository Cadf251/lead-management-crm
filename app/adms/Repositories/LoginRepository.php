<?php

namespace App\adms\Repositories;

use App\adms\Helpers\GenerateLog;
use App\adms\Database\DbOperations;

/**
 * Esta classe é responsável por interagir com a tabela de usuários de diferentes servidores
 * Ela herda a operação com o banco de dados e fornece métodos de encontrar o usuário com base no email e servidor.
 * 
 * Por herdar a DbOperations, ela pede uma conexão com o PDO no __contruct
 */
class LoginRepository extends DbOperations
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

    $execute = $this->executeSQL($query, $params);

    if (empty($execute) OR ($execute === false))
      return false;
    else
      return $execute[0];
  }
  
  /**
   * Verifica as permissões do usuário
   * Verifica também, à quais equipes ele tem acesso master
   * 
   */
  public function verificarPermissoes(int $nivId):?array
  {
    $query = <<<SQL
      SELECT 
        na.nome, 
        p.id
      FROM niveis_acesso na
      LEFT JOIN niveis_acesso_permissoes nap ON nap.nivel_acesso_id = na.id
      LEFT JOIN permissoes p ON p.id = nap.permissao_id
      WHERE na.id = :nivel_acesso_id
    SQL;

    $params = [
      ":nivel_acesso_id" => $nivId
    ];

    $permissoes = $this->executeSQL($query, $params);

    if (empty($permissoes)) {
      $permissoes = null;
    }

    return $permissoes;
  }

  public function acessoEquipes(int $usuarioId)
  {
    $query = <<<SQL
      SELECT id 
      FROM equipes e
      WHERE 
        (equipe_status_id = 3)
        AND
        EXISTS (
          SELECT 1 
          FROM equipes_usuarios eu
          WHERE eu.equipe_id = e.id
            AND eu.usuario_id = :usuario_id
            AND eu.equipe_usuario_funcao_id = 2
        )
    SQL;

    $params = [
      ":usuario_id" => $usuarioId
    ];

    $result = $this->executeSQL($query, $params);

    if (!empty($result) && ($result !== false)){
      $result = null;
    }

    return $result;
  }
}