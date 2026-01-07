<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperationsRefactored;
use App\adms\Models\teams\Equipe;
use App\adms\Models\teams\Colaborador;
use App\adms\Models\UserStatus;
use Exception;
use PDO;

/** Repositório de equipes */
class EquipesRepository
{
  /** @var string $tabela O nome da tabela no banco de dados */
  private string $tabela = "equipes";
  public DbOperationsRefactored $sql;
  
  public function __construct(PDO $conexao)
  {
    $this->sql = new DbOperationsRefactored($conexao);
  }

  /**
   * Retorna a query base para fazer consultas de equipes e trata as permissões também.
   * 
   * @param string $where As condições adicionais
   * 
   * @return string SQL
   */
  public function getQueryBase(string $where = ""): string
  {
    if ($where !== ""){
      $where = <<<SQL
        AND $where
      SQL;
    }

    return <<<SQL
      SELECT
        e.id AS equipe_id, e.nome AS equipe_nome, e.descricao AS equipe_descricao, e.created AS equipe_created, e.modified AS equipe_modified,
        e.equipe_status_id
      FROM {$this->tabela} e
      WHERE
        e.equipe_status_id != 1
        $where
      ORDER BY e.equipe_status_id DESC
    SQL;
  }

  /**
   * Retorna do banco de dados todas as equipes.
   * 
   * @return array|false
   */
  public function listarEquipes(): ?array
  {
    $query = $this->getQueryBase();

    $params = [
      "acesso_equipes" => $_SESSION["acesso_equipes"] ?? null
    ];

    try {
      $equipes = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    $final = [];

    foreach ($equipes as $equipe) {
      $object = $this->hydrateEquipe($equipe);
      $final[] = $object;
    }

    return $final;
  }

  
  /**
   * Seleciona apenas uma equipe
   * 
   * @param int $equipeId O ID da equipe
   * 
   * @return ?Equipe
   */
  public function selecionarEquipe(int $equipeId): ?Equipe
  {
    $query = $this->getQueryBase("e.id = :equipe_id");

    $params = [
      "equipe_id" => $equipeId
    ];

    try {
      $equipes = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($equipes)) {
      return null;
    }

    $equipe = $equipes[0];

    return $this->hydrateEquipe($equipe);
  }

  private function hydrateEquipe(array $row):?Equipe
  {
    $equipe = new Equipe();
    $equipe->setId($row["equipe_id"]);
    $equipe->setNome($row["equipe_nome"]);
    $equipe->setDescricao($row["equipe_descricao"]);
    $equipe->setColaboradores(
      $this->listarUsuarios($equipe->getId())
    );
    $equipe->setStatus($row["equipe_status_id"]);
    return $equipe;
  }

  /**
   * Lista os usuários háptos de uma equipe e seus detalhes.
   * 
   * @return array A lista de Colaborador
   */
  public function listarUsuarios(int $equipeId): array
  {
    $query = <<<SQL
      SELECT
        eu.id eu_id, eu.usuario_id, eu.vez, pode_receber_leads, eu.equipe_usuario_funcao_id,
        u.nome, u.nivel_acesso_id,
        euf.nome funcao_nome, euf.descricao funcao_desc
      FROM equipes_usuarios eu
      INNER JOIN usuarios u ON u.id = eu.usuario_id
      INNER JOIN equipes_usuarios_funcoes euf ON euf.id = eu.equipe_usuario_funcao_id
      WHERE 
        (eu.equipe_id = :equipe_id)
      ORDER BY equipe_usuario_funcao_id DESC
    SQL;

    $params = [
      ":equipe_id" => $equipeId
    ];

    $array = $this->sql->execute($query, $params);
    $final = [];

    foreach ($array as $row) {
      $final[] = $this->hydrateUsuario($row);
    }

    return $final;
  }

  public function selecionarUsuario(int $colaboradorId):?Colaborador
  {
    $query = <<<SQL
      SELECT
        eu.id eu_id, eu.usuario_id, eu.vez, pode_receber_leads, eu.equipe_usuario_funcao_id,
        u.nome, u.nivel_acesso_id,
        euf.nome funcao_nome, euf.descricao funcao_desc
      FROM equipes_usuarios eu
      INNER JOIN usuarios u ON u.id = eu.usuario_id
      INNER JOIN equipes_usuarios_funcoes euf ON euf.id = eu.equipe_usuario_funcao_id
      WHERE 
        eu.id = :colaborador_id
      ORDER BY equipe_usuario_funcao_id DESC
    SQL;

    $params = [
      ":colaborador_id" => $colaboradorId
    ];

    $array = $this->sql->execute($query, $params);

    try {
      if (empty($array) || $array === null) return null;
    } catch (Exception $e){
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
    
    return $this->hydrateUsuario($array[0]);
  }

  private function hydrateUsuario(array $row):?Colaborador
  {
    $usuario = new Colaborador();
    $usuario->setId($row["eu_id"]);
    $usuario->setUsuarioId(($row["usuario_id"]));
    $usuario->setUsuarioNome($row["nome"]);
    $usuario->setVez($row["vez"]);
    $usuario->setRecebeLeads($row["pode_receber_leads"]);
    $usuario->setNivelId($row["nivel_acesso_id"]);
    $usuario->setFuncao(
      $row["equipe_usuario_funcao_id"],
      $row["funcao_nome"],
      $row["funcao_desc"]
    );
    return $usuario;
  }

  public function salvar(Equipe $equipe)
  {
    $params = [
      "nome" => $equipe->getNome(),
      "descricao" => $equipe->getDescricao() ?? null,
      "equipe_status_id" => $equipe->getStatusId(),
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->tabela, $params, $equipe->getId());
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /** 
   * Cria o registro de uma equipe no banco de dados
   * 
   * @param Equipe $equipe
   * 
   */
  public function criarEquipe(Equipe $equipe): void
  {
    $params = [
      "nome" => $equipe->getNome(),
      "descricao" => $equipe->getDescricao() ?? null,
      "created" => date($_ENV['DATE_FORMAT'])
    ];
    
    try {
      $equipe->setId($this->sql->insert($this->tabela, $params));
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  /**
   * Retorna os usuários que não estão na equipe e estão ativos
   * 
   * @param Equipe $equipe
   * 
   * @return array
   */
  public function eleitosAEquipe(Equipe $equipe):array
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
      $eleitos = $this->sql->execute($usuariosQ, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    $final = [];
    foreach ($eleitos as $eleito) {
      $final[] = $this->hydrateNewUsuario($eleito);
    }

    return $final;
  }

  private function hydrateNewUsuario(array $row):?Colaborador
  {
    $usuario = Colaborador::novo(
      $row["id"],
      $row["nome"],
      $row["nivel_acesso_id"]
    );
    return $usuario;
  }

  public function getNivel(int $usuarioId):?int
  {
    $query = <<<SQL
    SELECT nivel_acesso_id
    FROM usuarios
    WHERE id = :usuario_id
    SQL;

    $params = [
      "usuario_id" => $usuarioId
    ];

    $result = $this->sql->execute($query, $params);

    if(empty($result)) return null;

    return $result[0]["nivel_acesso_id"];
  }

  public function getFuncao(int $funcaoId):?array
  {
    $query = <<<SQL
    SELECT id, nome, descricao
    FROM equipes_usuarios_funcoes
    WHERE id = :funcao_id
    SQL;

    $params = [
      "funcao_id" => $funcaoId
    ];

    try {
      $result = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if(empty($result)) return null;

    return $result[0];
  }

  public function criarColaborador(Equipe $equipe, Colaborador $colab):void
  {
    $params = [
      "vez" => (int)$colab->getVez(),
      "pode_receber_leads" => (int)$colab->podeReceberLeads(),
      "equipe_usuario_funcao_id" => $colab->getFuncaoId(),
      "usuario_id" => $colab->getUsuarioId(),
      "equipe_id" => $equipe->getId(),
    ];

    try {
      $id = $this->sql->insert("equipes_usuarios", $params);
      $colab->setId($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function removerColaborador(Colaborador $colaborador):void
  {
    try {
      $this->sql->deleteByIdSQL("equipes_usuarios", $colaborador->getId());
    } catch (Exception $e){
      throw new Exception("Não foi possível deletar um colaborador no banco de dados.", $e->getCode(), $e);
    }
  }

  public function salvarColaborador(Colaborador $colaborador)
  {
    $params = [
      "vez" => $colaborador->getVez(),
      "pode_receber_leads" => $colaborador->podeReceberLeads(),
      "equipe_usuario_funcao_id" => $colaborador->getFuncaoId()
    ];

    try {
      $this->sql->updateById("equipes_usuarios", $params, $colaborador->getId());
    } catch (Exception $e) {
      throw new Exception("Não foi possível salvar um colaborador no banco de dados.", $e->getCode(), $e);
    }
  }
}
