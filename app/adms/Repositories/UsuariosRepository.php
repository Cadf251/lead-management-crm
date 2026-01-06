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
        id, nome, email, celular, senha, foto_perfil, nivel_acesso_id AS niv_id, usuario_status_id AS us_id
      FROM {$this->tabela} u
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
        CASE WHEN us_id = 2 THEN 1 ELSE 0 END, us_id DESC, id
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
    $usuario->setId($row["id"]);
    $usuario->setNome($row["nome"]);
    $usuario->setEmail($row["email"]);
    $usuario->setCelular($row["celular"]);
    $usuario->setSenha($row["senha"]);
    $usuario->setFoto($row["foto_perfil"]);
    $usuario->setNivel($row["niv_id"]);
    $usuario->setStatus($row["us_id"]);
    return $usuario;
  }

  /**
   * Salva o objeto no banco de dados
   * 
   * @param Usuario $usuario
   */
  public function salvar(Usuario $usuario):void {
    $params = [
      "nome" => $usuario->getNome(),
      "email" => $usuario->getEmail(),
      "celular" => $usuario->getEmail(),
      "senha" => $usuario->getSenhaHash(),
      "foto_perfil" => $usuario->getFoto(),
      "usuario_status_id" => $usuario->getStatusId(),
      "nivel_acesso_id" => $usuario->getNivelAcessoId(),
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->tabela, $params, $usuario->getId());
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
      "nome" => $usuario->getNome(),
      "email" => $usuario->getEmail(),
      "celular" => $usuario->getCelular(),
      "foto_perfil" => $usuario->getFoto(),
      "nivel_acesso_id" => $usuario->getNivelAcessoId(),
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