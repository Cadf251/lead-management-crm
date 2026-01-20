<?php

namespace App\adms\Repositories;

use App\adms\Models\teams\Team;
use Exception;

/** Repositório de equipes */
class TeamsRepository extends RepositoryBase
{
  /** @var string $table O nome da tabela no banco de dados */
  private string $table = "equipes";

  /**
   * Retorna a query base para fazer consultas de equipes e trata as permissões também.
   * 
   * @param string $where As condições adicionais
   * 
   * @return string SQL
   */
  private function getQueryBase(string $where = ""): string
  {
    if ($where !== "") {
      $where = <<<SQL
        AND $where
      SQL;
    }

    return <<<SQL
      SELECT
        e.id AS equipe_id, e.nome AS equipe_nome, e.descricao AS equipe_descricao, e.created AS equipe_created, e.modified AS equipe_modified,
        e.equipe_status_id
      FROM {$this->table} e
      WHERE
        e.equipe_status_id != 1
        $where
      ORDER BY e.equipe_status_id DESC
    SQL;
  }

  /**
   * Retorna do banco de dados todas as equipes.
   * 
   * @return ?array
   */
  public function list(): ?array
  {
    try {
      return $this->sql->selectMultiple(
        $this->getQueryBase(),
        fn(array $row) => $this->hydrate($row)
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Seleciona apenas uma equipe
   * 
   * @param int $teamId O ID da equipe
   * 
   * @return ?Team
   */
  public function select(int $teamId): ?Team
  {
    try {
      return $this->sql->selectOne(
        $this->getQueryBase("e.id = :equipe_id"),
        fn(array $row) => $this->hydrate($row),
        ["equipe_id" => $teamId]
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  private function hydrate(array $row): ?Team
  {
    $equipe = new Team();
    $equipe->setId($row["equipe_id"]);
    $equipe->setName($row["equipe_nome"]);
    $equipe->setDescription($row["equipe_descricao"]);
    $equipe->setStatus($row["equipe_status_id"]);
    return $equipe;
  }

  public function save(Team $team)
  {
    $params = [
      "nome" => $team->getName(),
      "descricao" => $team->getDescription() ?? null,
      "equipe_status_id" => $team->getStatusId(),
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->table, $params, $team->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /** 
   * Cria o registro de uma equipe no banco de dados
   * 
   * @param Team $equipe
   */
  public function create(Team $equipe): void
  {
    $params = [
      "nome" => $equipe->getName(),
      "descricao" => $equipe->getDescription() ?? null,
      "created" => date($_ENV['DATE_FORMAT'])
    ];

    try {
      $equipe->setId($this->sql->insert($this->table, $params));
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}
