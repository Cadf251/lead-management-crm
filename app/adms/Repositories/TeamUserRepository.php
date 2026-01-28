<?php

namespace App\adms\Repositories;

use App\adms\Models\teams\Team;
use App\adms\Models\teams\TeamUser;
use App\adms\Models\UserStatus;
use Exception;

class TeamUserRepository extends RepositoryBase
{
  private function getQueryBase(string $where = ""): string
  {
    return <<<SQL
      SELECT
        eu.id eu_id, eu.usuario_id, eu.vez, pode_receber_leads, eu.equipe_usuario_funcao_id,
        u.nome, u.nivel_acesso_id
      FROM equipes_usuarios eu
      INNER JOIN usuarios u ON u.id = eu.usuario_id
      WHERE 
        $where
      ORDER BY equipe_usuario_funcao_id DESC
    SQL;
  }

  /**
   * Lista os usuários háptos de uma equipe e seus detalhes.
   * 
   * @return array A lista de Colaborador
   */
  public function list(int $teamId): ?array
  {
    try {
      return $this->sql->selectMultiple(
        $this->getQueryBase("eu.equipe_id = :equipe_id"),
        fn(array $row) => $this->hydrate($row),
        ["equipe_id" => $teamId]
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function select(int $teamUserId): ?TeamUser
  {
    try {
      return $this->sql->selectOne(
        $this->getQueryBase("eu.id = :colaborador_id"),
        fn(array $row) => $this->hydrate($row),
        ["colaborador_id" => $teamUserId]
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  private function hydrate(array $row): ?TeamUser
  {
    $usuario = new TeamUser();
    $usuario->setId($row["eu_id"]);
    $usuario->setUserId(($row["usuario_id"]));
    $usuario->setUserName($row["nome"]);
    $usuario->setTime($row["vez"]);
    $usuario->setReceiveLeads($row["pode_receber_leads"]);
    $usuario->setLevelId($row["nivel_acesso_id"]);
    $usuario->setFunction($row["equipe_usuario_funcao_id"]);
    return $usuario;
  }

  /**
   * Retorna os usuários que não estão na equipe e estão ativos
   * 
   * @param Equipe $equipe
   * 
   * @return array
   */
  public function listAbleForTeam(Team $equipe): ?array
  {
    $usuariosQ = <<<SQL
    SELECT 
      u.id, u.nome, nivel_acesso_id
    FROM usuarios u
    WHERE 
      (u.id NOT IN (
        SELECT usuario_id FROM equipes_usuarios WHERE equipe_id = :equipe_id
      )) AND (u.usuario_status_id = :usuario_id)
    SQL;

    $params = [
      "equipe_id" => $equipe->getId(),
      "usuario_id" => UserStatus::STATUS_ATIVADO
    ];

    try {
      return $this->sql->selectMultiple(
        $usuariosQ,
        fn(array $row) => $this->hydrateNew($row),
        $params
      );
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  private function hydrateNew(array $row): ?TeamUser
  {
    $usuario = TeamUser::new(
      $row["id"],
      $row["nome"],
      $row["nivel_acesso_id"]
    );

    return $usuario;
  }

  public function create(Team $equipe, TeamUser $colab): void
  {
    $params = [
      "vez" => (int)$colab->getTime(),
      "pode_receber_leads" => (int)$colab->canReceiveLeads(),
      "equipe_usuario_funcao_id" => $colab->getFunctionId(),
      "usuario_id" => $colab->getUserId(),
      "equipe_id" => $equipe->getId(),
    ];

    try {
      $id = $this->sql->insert("equipes_usuarios", $params);
      $colab->setId($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function delete(TeamUser $colaborador): void
  {
    try {
      $this->sql->deleteById("equipes_usuarios", $colaborador->getId());
    } catch (Exception $e) {
      throw new Exception("Não foi possível deletar um colaborador no banco de dados.", $e->getCode(), $e);
    }
  }

  public function save(TeamUser $colaborador)
  {
    $params = [
      "vez" => $colaborador->getTime(),
      "pode_receber_leads" => $colaborador->canReceiveLeads(),
      "equipe_usuario_funcao_id" => $colaborador->getFunctionId()
    ];

    try {
      $this->sql->updateById("equipes_usuarios", $params, $colaborador->getId());
    } catch (Exception $e) {
      throw new Exception("Não foi possível salvar um colaborador no banco de dados.", $e->getCode(), $e);
    }
  }

  public function getUserInfo(int $usuarioId): ?array
  {
    $query = <<<SQL
    SELECT nome, nivel_acesso_id
    FROM usuarios
    WHERE id = :usuario_id
    SQL;

    $params = [
      "usuario_id" => $usuarioId
    ];

    try {
      $result = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($result)) return null;

    return $result[0];
  }
}
