<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbConnection;

/**
 * Recupera os dados de usuários
 */
class UsuariosRepository extends DbConnection
{
  /** @var string $tabela é o nome da tabela no banco de dados */
  public string $tabela = "usuarios";

  /**
   * Retorna a base de SQL para consulta de usuários
   * 
   * Retorn um array no padrão:
   * 
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
        u.id u_id, u.nome u_nome, email u_email, u.celular u_celular, u.foto_perfil u_foto_perfil, u.senha u_senha,
        niv.nome niv_nome, niv.descricao niv_descricao,
        us.id us_id, us.nome us_nome, us.descricao us_descricao
      FROM {$this->tabela} u
      INNER JOIN niveis_acesso niv ON niv.id = u.nivel_acesso_id
      INNER JOIN usuario_status us ON u.usuario_status_id = us.id
    SQL;
  }

  /**
   * Lista todos os usuários com JOINs de nivel de acesso e status.
   * 
   * @return array Sintaxe: u_campo, niv_campo, us_campo
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
}