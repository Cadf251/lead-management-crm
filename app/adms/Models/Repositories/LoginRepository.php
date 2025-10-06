<?php

namespace App\adms\Models\Repositories;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbOperations;

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
   * Verifica se o usuário existe
   * 
   * @param string $email O email do usuário a ser consultado
   * 
   * @return bool|array Falso se não existir, array simplificado se existir
   */
  public function verificarUsuario(string $email) :bool|array
  {
    $query = <<<SQL
      SELECT
        id, nome, email, senha, nivel_acesso_id, foto_perfil, usuario_status_id
      FROM usuarios
      WHERE email = :email
      LIMIT 1
    SQL;

    $params = [
      ":email" => $email
    ];

    $execute = $this->executeSQL($query, $params);

    if (empty($execute) || $execute === false)
      return false;
    else
      return $execute[0];
  }

  /**
   * Verifica as permissões do usuário
   * Verifica também, à quais equipes ele tem acesso master
   * 
   */
  public function verificarPermissoes(int $nivId)
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

    if (empty($permissoes))
      return false;

    $_SESSION["permissoes"] = [];
    $_SESSION["nivel_acesso_nome"] = $permissoes[0]["nome"];

    foreach ($permissoes as $permissao){
      $_SESSION["permissoes"][] = $permissao["id"];
    }

    // Se ele tiver a permissão 4, deve verificar em quais equipes ele tem 100% de acesso
    if(in_array(4, $_SESSION["permissoes"])){
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
        ":usuario_id" => $_SESSION["usuario_id"]
      ];

      $equipes = $this->executeSQL($query, $params);

      if (!empty($equipes) && ($equipes !== false))
        $_SESSION["acesso_equipes"] = implode(", ", $equipes);
      else
        GenerateLog::generateLog(
          "info",
          "Resultado inesperado, ao fazer login, usuário com a permissão 4 (gerenciar equipes atribuidas), não retornou nenhum equipe como função 2 (gerente).",
          ["resultado consulta" => $equipes, "permissoes" => $_SESSION["permissoes"]]
        );
    }
  }

  /**
   * Insere uma nova senha no banco de dados e seta o status do usuário como ativo.
   * 
   * @param string $senhaHash A senha com hash
   * @param int $id O ID do usuário
   * 
   * @return bool
   */
  public function registrarSenha(string $senhaHash, int $id):bool
  {
    $params = [
      ":senha" => $senhaHash,
      ":usuario_status_id" => 3,
      ":modified" => date($_ENV['DATE_FORMAT'])
    ];

    GenerateLog::generateLog("info", "Data teste", ["modified" => $params[":modified"]]);

    return $this->updateSQL("usuarios", $params, $id);
  }
}