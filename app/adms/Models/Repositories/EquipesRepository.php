<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbOperations;

/** Repositório de equipes */
class EquipesRepository extends DbOperations
{
  /**
   * Retorna do banco de dados todas as equipes.
   * 
   * @return array|false
   */
  public function listarEquipes():array|false
  {
    $base = <<<SQL
    SELECT
      e.id AS equipe_id, e.nome AS equipe_nome, e.descricao AS equipe_descricao,e.created AS equipe_created, e.modified AS equipe_modified,
      p.nome AS produto_nome, p.descricao AS produto_descricao,
      es.id AS equipe_status_id, es.nome AS equipe_status_nome
    FROM equipes e
    INNER JOIN produtos p ON p.id = e.produto_id
    INNER JOIN generico_status es ON es.id = e.equipe_status_id
    WHERE
      (e.equipe_status_id != 1)
    SQL;

    // Tratar permissões
    if (in_array(2, $_SESSION["permissoes"]))
      $query = $base;
    else if (in_array(4, $_SESSION["permissoes"]))
      $query = <<<SQL
      $base
        AND (e.id IN(:acesso_equipes))
      SQL;

    $params = [
      ":usuario_id" => $_SESSION["usuario_id"],
      ":acesso_equipes" => $_SESSION["acesso_equipes"]
    ];

    return $this->executeSQL($query, $params);
  }

  /**
   * Lista os usuários haptos de uma equipe e seus detalhes.
   * 
   * @return array A lista de usuários
   */
  public function listarUsuarios():array
  {
    return [];
  }
}