<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbOperations;

/** Manipula os dados de usuários no banco de dados */
class UsuariosRepository extends DbOperations
{
  /** @var string $tabela é o nome da tabela no banco de dados */
  public string $tabela = "usuarios";

  /**
   * Retorna a base de SQL para consulta de usuários
   * Retorna um array no padrão:
   * tabela_campo;
   * u_id (usuario.id)
   * niv_nome (nivel_acesso.nome)
   * us_id (usuario_status.id)
   * 
   * @return string A query SELECT
   */
  private function queryBase() :string
  {
    return <<<SQL
      SELECT 
        u.id u_id, u.nome u_nome, email u_email, u.celular u_celular, u.foto_perfil u_foto_perfil,
        niv.id niv_id, niv.nome niv_nome, niv.descricao niv_descricao,
        us.id us_id, us.nome us_nome, us.descricao us_descricao
      FROM {$this->tabela} u
      INNER JOIN niveis_acesso niv ON niv.id = u.nivel_acesso_id
      INNER JOIN usuario_status us ON u.usuario_status_id = us.id
    SQL;
  }

  /**
   * Lista todos os usuários sem distinção de status usando a $this->queryBase().
   * 
   * @return array Sintaxe: 0 => [u_campo, niv_campo, us_campo] 1=> [...]
   */

  public function listar() :array
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      ORDER BY
        CASE WHEN us_id = 2 THEN 1 ELSE 0 END, us_id DESC, u.id
    SQL;

    $array = $this->executeSQL($query);

    return $array;
  }

  /**
   * Seleciona um único usuário com base no ID. Usa a $this->queryBase(); ATENÇÃO: Não resume o array.
   * 
   * @param int $id O ID do usuário a ser recuperado
   * 
   * @return array Sintaxe: 0 => [u_campo, niv_campo, us_campo] 1=> [...]
   */
  public function selecionar(int $id) :array
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE 
        u.id = :usuario_id
      LIMIT 1
    SQL;

    $params = [
      ":usuario_id" => $id
    ];

    return $this->executeSQL($query, $params);
  }

  /**
   * Atualiza os usuários, e já incluir o modified no $params
   * 
   * @param array $params Os parâmetros que devem ser ataulizados, no formato: [":campo_literal" => "var"]
   * @param int $id O id do usuário que será atualizado
   * 
   * @return bool
   */
  public function updateUsuario(array $params, int $id)
  {
    $modified = date($_ENV["DATE_FORMAT"]);
    $params[":modified"] = $modified;
    return $this->updateSQL($this->tabela, $params, $id);
  }
  
  /**
   * Seta o status do usuário como aguardando confirmação
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @return bool
   */
  public function ativar(int $usuarioId) :bool
  {
    $params = [
      ":usuario_status_id" => 1
    ];

    return $this->updateUsuario($params, $usuarioId);
  }

  /**
 * Seta o status do usuário como aguardando confirmação e apaga a senha
 * 
 * @param int $usuarioId O ID do usuário
 * 
 * @return bool
  */
  public function resetarSenha(int $usuarioId) :bool
  {
    $params = [
      ":usuario_status_id" => 1,
      ":senha" => null
    ];

    return $this->updateUsuario($params, $usuarioId);
  }

  /**
   * Seta o status do usuário como desativado
   * 
   * @param int $usuarioId O ID do usuário
   * 
   * @return bool
   */
  public function desativar(int $usuarioId) :bool
  {
    $queries[] = <<<SQL
    UPDATE {$this->tabela} SET
      usuario_status_id = 2,
      senha = null,
      modified = :data_now
    WHERE
      id = :usuario_id
    SQL;

    $params = [
      ":data_now" => date($_ENV["DATE_FORMAT"]),
      ":usuario_id" => $usuarioId
    ];

    $queries[] =
    "DELETE FROM equipes_usuarios WHERE equipes_usuarios.usuario_id = :usuario_id";

    $queries[] =
    "DELETE FROM tokens WHERE tokens.usuario_id = :usuario_id";

    $sucesso = false;
    foreach ($queries as $query){
      $sucesso = $this->executeSQL($query, $params, false);
      if ($sucesso === false)
        return false;
    }
    return true;
  }

  /** 
   * Dá um tiro de misericórdia no usuário.
   * 
   * @param int $usuarioId O Usuário que será excluído
   * 
   * @return bool
   */
  public function excluir(int $usuarioId):bool
  {
    return $this->deleteByIdSQL($this->tabela, $usuarioId);
  }
}