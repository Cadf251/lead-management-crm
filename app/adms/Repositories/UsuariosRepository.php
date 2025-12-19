<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperationsRefactored;
use App\adms\Models\Usuario;
use Exception;
use PDO;

/** Manipula os dados de usuários no banco de dados */
class UsuariosRepository
{
  /** @var string $tabela é o nome da tabela no banco de dados */
  public string $tabela = "usuarios";

  public DbOperationsRefactored $sql;

  public function __construct(PDO $conexao)
  {
    $this->sql = new DbOperationsRefactored($conexao);
  }
  
  /**
   * Retorna a base de SQL para consulta de usuários
   * 
   * @return string A query SELECT
   */
  private function queryBase() :string
  {
    return <<<SQL
      SELECT 
        u.id u_id, u.nome u_nome, email u_email, u.celular u_celular, u.senha u_senha, u.foto_perfil u_foto_perfil,
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
   * @return array Usuario
   */
  public function listar() :array
  {
    $query = $this->queryBase();
    $query .= <<<SQL
      ORDER BY
        CASE WHEN us_id = 2 THEN 1 ELSE 0 END, us_id DESC, u.id
    SQL;

    try {
      $array = $this->sql->execute($query);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    $final = [];
    
    foreach($array as $row){
      $final[] = $this->hydrateUsuario($row);
    }

    return $final;
  }

  /**
   * Seleciona um único usuário com base no ID
   * 
   * @param int $id O ID do usuário a ser recuperado
   * 
   * @return Usuario
   */
  public function selecionar(int $id):?Usuario
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

    return $this->hydrateUsuario($array[0]);
  }

  public function selecionarByEmail(string $email):?Usuario
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

    return $this->hydrateUsuario($array[0]);
  }

  /**
   * Transforma um usuário array em Usuario
   * 
   * @param array $row O row vindo do queryBase
   * 
   * @return ?Usuario
   */
  public function hydrateUsuario(array $row):?Usuario{
    $usuario = new Usuario();
    $usuario->setId($row["u_id"]);
    $usuario->setNome($row["u_nome"]);
    $usuario->setEmail($row["u_email"]);
    $usuario->setCelular($row["u_celular"]);
    $usuario->setSenha($row["u_senha"]);
    $usuario->setFoto($row["u_foto_perfil"]);
    $usuario->setNivel($row["niv_id"], $row["niv_nome"], $row["niv_descricao"]);
    $usuario->setStatus($row["us_id"], $row["us_nome"], $row["us_descricao"]);
    return $usuario;
  }

  /**
   * Salva o objeto no banco de dados
   * 
   * @param Usuario $usuario
   */
  public function salvar(Usuario $usuario):void {
    $params = [
      "nome" => $usuario->nome,
      "email" => $usuario->email,
      "celular" => $usuario->celular,
      "senha" => $usuario->senhaHash,
      "foto_perfil" => $usuario->foto,
      "usuario_status_id" => $usuario->status->id,
      "nivel_acesso_id" => $usuario->nivel->id,
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->tabela, $params, $usuario->id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function existe(string $email)
  {
    return $this->sql->existe(
      $this->tabela,
      [["email", "="]],
      ["email" => $email]
    );
  }

  /**
   * Cria um novo usuário
   * 
   * @param Usuario $usuario
   * 
   * @return int O id do usuário
   */
  public function criar(Usuario $usuario):int
  {
    $params = [
      "nome" => $usuario->nome,
      "email" => $usuario->email,
      "celular" => $usuario->celular,
      "foto_perfil" => $usuario->foto,
      "nivel_acesso_id" => $usuario->nivel->id,
      "created" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      return $this->sql->insert($this->tabela, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function getNivel(int $id){
    return $this->getStatusOuNivel($id, "niveis_acesso")[0];
  }

  public function getStatus(int $id){
    return $this->getStatusOuNivel($id, "usuario_status")[0];
  }

  private function getStatusOuNivel(int $id, string $tabela){
    $query = <<<SQL
    SELECT nome, descricao
    FROM $tabela
    WHERE id = :id
    SQL;

    $params = [
      "id" => $id
    ];
    try {
      return $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }
}