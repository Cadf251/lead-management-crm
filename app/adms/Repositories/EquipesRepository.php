<?php

namespace App\adms\Repositories;

use App\adms\Database\DbOperationsRefactored;
use App\adms\Helpers\GenerateLog;
use App\adms\Models\Equipe;
use App\adms\Models\EquipeFuncao;
use App\adms\Models\EquipeUsuario;
use App\adms\Models\Produto;
use App\adms\Models\Usuario;
use App\adms\Models\UsuarioStatus;
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
    $base = <<<SQL
      SELECT
        e.id AS equipe_id, e.nome AS equipe_nome, e.descricao AS equipe_descricao, e.created AS equipe_created, e.modified AS equipe_modified,
        p.id AS produto_id, p.nome AS produto_nome, p.descricao AS produto_descricao,
        es.id AS equipe_status_id, es.nome AS equipe_status_nome
      FROM {$this->tabela} e
      INNER JOIN produtos p ON p.id = e.produto_id
      INNER JOIN generico_status es ON es.id = e.equipe_status_id
      WHERE
        e.equipe_status_id != 1
        $where
      ORDER BY e.equipe_status_id DESC
    SQL;

    // Tratar permissões
    if (in_array(2, $_SESSION["permissoes"]))
      $query = $base;
    else if (in_array(4, $_SESSION["permissoes"]))
      $query = <<<SQL
      $base
        AND (e.id IN(:acesso_equipes))
      SQL;

    return $query;
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
      "acesso_equipes" => $_SESSION["acesso_equipes"],
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
      $this->listarUsuarios($equipe->id)
    );
    $equipe->setProdutoByArray([
      "id" => $row["produto_id"],
      "nome" => $row["produto_nome"],
      "descricao" => $row["produto_descricao"] ?? null
    ]);
    $equipe->setStatus($row["equipe_status_id"]);
    return $equipe;
  }

  /**
   * Lista os usuários háptos de uma equipe e seus detalhes.
   * 
   * @return array A lista de EquipeUsuario
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

  private function hydrateUsuario(array $row):?EquipeUsuario
  {
    $usuario = new EquipeUsuario();
    $usuario->setId($row["eu_id"]);
    $usuario->setUsuarioId(($row["usuario_id"]));
    $usuario->setUsuarioNome($row["nome"]);
    $usuario->setVez($row["vez"]);
    $usuario->setRecebeLeads($row["pode_receber_leads"]);
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
      "nome" => $equipe->nome,
      "descricao" => $equipe->descricao,
      "produto_id" => $equipe->produto->id,
      "equipe_status_id" => $equipe->status->id,
      "modified" => date($_ENV["DATE_FORMAT"])
    ];

    try {
      $this->sql->updateById($this->tabela, $params, $equipe->id);
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
      "nome" => $equipe->nome,
      "descricao" => $equipe->descricao,
      "produto_id" => $equipe->produto->id,
      "created" => date($_ENV['DATE_FORMAT'])
    ];
    
    try {
      $equipe->setId($this->sql->insert($this->tabela, $params));
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function getProdutoById(int $produtoId):?Produto
  {
    $query = <<<SQL
    SELECT id, nome, descricao
    FROM produtos
    WHERE id = :id
    SQL;

    $params = [
      "id" => $produtoId
    ];

    try {
      $produto = $this->sql->execute($query, $params);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (empty($produto)) return null;

    return $this->hydrateProduto($produto[0]);
  }

  private function hydrateProduto(array $row):?Produto
  {
    return new Produto(
      $row["id"],
      $row["nome"],
      $row["descricao"] ?? null
    );
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
      "equipe_id" => $equipe->id,
      "usuario_id" => Usuario::STATUS_ATIVADO
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

  private function hydrateNewUsuario(array $row):?EquipeUsuario
  {
    $usuario = EquipeUsuario::novo(
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

  public function criarColaborador(Equipe $equipe, EquipeUsuario $colab):void
  {
    $params = [
      "vez" => (int)$colab->vez,
      "pode_receber_leads" => (int)$colab->recebeLeads(),
      "equipe_usuario_funcao_id" => $colab->funcao->id,
      "usuario_id" => $colab->usuarioId,
      "equipe_id" => $equipe->id,
    ];

    try {
      $id = $this->sql->insert("equipes_usuarios", $params);
      $colab->setId($id);
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
  }

  // public function vezUsuario($registroId)
  // {
  //   $query = <<<SQL
  //   SELECT vez 
  //   FROM equipes_usuarios 
  //   WHERE 
  //     id = :id
  //   LIMIT 1
  //   SQL;

  //   $params = [
  //     ":id" => $registroId
  //   ];

  //   $minvez = $this->executeSQL($query, $params, true)[0];
  //   if ($minvez["vez"] === null) return 0;
  //   return $minvez["vez"];
  // }

  // /**
  //  * Adiciona um usuário em uma equipe com base no $params
  //  */
  // public function adicionarUsuario(array $params): bool
  // {
  //   return $this->insertSQL("equipes_usuarios", $params);
  // }

  // /**
  //  * Pega o ID do registro de equipes_usuarios
  //  */
  // public function getIdEquipesUsuarios(int $equipeId, int $usuarioId)
  // {
  //   $query = <<<SQL
  //     SELECT id
  //     FROM equipes_usuarios
  //     WHERE
  //       equipe_id = :equipe_id
  //       AND usuario_id = :usuario_id
  //   SQL;

  //   $params = [
  //     ":equipe_id" => $equipeId,
  //     ":usuario_id" => $usuarioId
  //   ];

  //   $array = $this->executeSQL($query, $params);
  //   return $array[0]["id"];
  // }

  // /**
  //  * Troca se o usuário pode receber leads ou não
  //  */
  // public function alterarRecebimento(int $equipeId, int $usuarioId, int $set)
  // {
  //   $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);

  //   $vez = $this->minVez($equipeId);

  //   $params = [
  //     ":vez" => $vez,
  //     ":pode_receber_leads" => $set
  //   ];

  //   return $this->updateSQL("equipes_usuarios", $params, $registroId);
  // }

  // public function priorizar(int $equipeId, int $usuarioId):bool
  // {
  //   return $this->mudarVez($equipeId, $usuarioId, 1);
  // }

  // public function prejudicar(int $equipeId, int $usuarioId):bool
  // {
  //   return $this->mudarVez($equipeId, $usuarioId, -1);
  // }

  // public function mudarVez(int $equipeId, int $usuarioId, int $set):bool
  // {
  //   $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);

  //   $vez = $this->vezUsuario($registroId);

  //   if (($set === -1) && $vez === 0)
  //     return false;

  //   $vez = $vez + $set;

  //   $params = [
  //     ":vez" => $vez
  //   ];

  //   return $this->updateSQL("equipes_usuarios", $params, $registroId);
  // }

  // public function retirarUsuario(int $equipeId, int $usuarioId)
  // {
  //   $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);
  //   return $this->deleteByIdSQL("equipes_usuarios", $registroId);
  // }
}
