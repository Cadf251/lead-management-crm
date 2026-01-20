<?php

namespace App\adms\Repositories;

use App\adms\Models\users\User;
use Exception;

/** 
 * Manipula os dados de usuários no banco de dados
 */
class UsersRepository extends RepositoryBase
{
  /** @var string $tabela é o nome da tabela no banco de dados */
  private string $table = "usuarios";

  /**
   * Retorna a base de SQL para consulta de usuários
   * 
   * @return string A query SELECT
   */
  private function queryBase() :string
  {
    return <<<SQL
      SELECT 
        id, nome, email, celular, senha, foto_perfil, nivel_acesso_id AS niv_id, usuario_status_id AS us_id
      FROM {$this->table} u
    SQL;
  }

  /**
   * Lista todos os usuários sem distinção de status usando a $this->queryBase().
   * 
   * @return array Usuario
   */
  public function list() :array
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      ORDER BY
        CASE WHEN us_id = 2 THEN 1 ELSE 0 END, us_id DESC, id
    SQL;

    try {
      return $this->sql->selectMultiple($query, fn(array $row) => $this->hydrate($row));
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Seleciona um único usuário com base no ID
   * 
   * @param int $id O ID do usuário a ser recuperado
   * 
   * @return User
   */
  public function select(int $id):?User
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE 
        u.id = :usuario_id
      LIMIT 1
    SQL;

    $params = [
      "usuario_id" => $id
    ];

    try {
      $array = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($array)) {
      return null;
    }

    return $this->hydrate($array[0]);
  }

  public function selectByEmail(string $email):?User
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      WHERE 
        u.email = :email
      LIMIT 1
    SQL;

    $params = [
      "email" => $email
    ];

    try {
      $array = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($array)) {
      return null;
    }

    return $this->hydrate($array[0]);
  }

  /**
   * Transforma um usuário array em Usuario
   * 
   * @param array $row O row vindo do queryBase
   * 
   * @return ?User
   */
  public function hydrate(array $row):?User{
    $usuario = new User();
    $usuario->setId($row["id"]);
    $usuario->setName($row["nome"]);
    $usuario->setEmail($row["email"]);
    $usuario->setPhone($row["celular"]);
    $usuario->setPass($row["senha"]);
    $usuario->setProfilePicture($row["foto_perfil"]);
    $usuario->setNivel($row["niv_id"]);
    $usuario->setStatus($row["us_id"]);
    return $usuario;
  }

  /**
   * Salva o objeto no banco de dados
   * 
   * @param User $usuario
   */
  public function save(User $usuario):void {
    $params = [
      "nome" => $usuario->getName(),
      "email" => $usuario->getEmail(),
      "celular" => $usuario->getPhone(),
      "senha" => $usuario->getPassWordHash(),
      "foto_perfil" => $usuario->getProfilePicture(),
      "usuario_status_id" => $usuario->getStatusId(),
      "nivel_acesso_id" => $usuario->getSystemLevelId(),
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->table, $params, $usuario->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function exists(string $email)
  {
    return $this->sql->existe(
      $this->table,
      [["email", "="]],
      ["email" => $email]
    );
  }

  /**
   * Cria um novo usuário
   * 
   * @param User $usuario
   * 
   * @return int O id do usuário
   */
  public function create(User $usuario):int
  {
    $params = [
      "nome" => $usuario->getName(),
      "email" => $usuario->getEmail(),
      "celular" => $usuario->getPhone(),
      "foto_perfil" => $usuario->getProfilePicture(),
      "nivel_acesso_id" => $usuario->getSystemLevelId(),
      "created" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      return $this->sql->insert($this->table, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}