<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbOperations;

/** Repositório de equipes */
class EquipesRepository extends DbOperations
{
  /** @var string $tabela O nome da tabela no banco de dados */
  private string $tabela = "equipes";

  /**
   * Retorna a query base para fazer consultas de equipes e trata as permissões também.
   * 
   * @param string $where As condições adicionais
   * 
   * @return string SQL
   */
  public function getQueryBase(string $where = ""):string
  {
    if ($where !== "")
      $where = <<<SQL
        WHERE
          $where
      SQL;

    $base = <<<SQL
      SELECT
        e.id AS equipe_id, e.nome AS equipe_nome, e.descricao AS equipe_descricao, e.created AS equipe_created, e.modified AS equipe_modified,
        p.id AS produto_id, p.nome AS produto_nome, p.descricao AS produto_descricao,
        es.id AS equipe_status_id, es.nome AS equipe_status_nome
      FROM {$this->tabela} e
      INNER JOIN produtos p ON p.id = e.produto_id
      INNER JOIN generico_status es ON es.id = e.equipe_status_id
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
  public function listarEquipes(): array|false
  {
    $query = $this->getQueryBase();

    $params = [
      ":acesso_equipes" => $_SESSION["acesso_equipes"]
    ];

    return $this->executeSQL($query, $params);
  }

  /**
   * Lista os usuários háptos de uma equipe e seus detalhes.
   * 
   * @return array A lista de usuários
   */
  public function listarUsuarios(int $equipeId): array
  {
    $query = <<<SQL
      SELECT
        eu.usuario_id, eu.vez, pode_receber_leads, eu.equipe_usuario_funcao_id,
        u.nome, u.nivel_acesso_id,
        euf.nome funcao_nome
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

    return $this->executeSQL($query, $params);
  }

  /**
   * Seleciona apenas uma equipe
   * 
   * @param int $equipeId O ID da equipe
   * 
   * @return array|false
   * 
   */
  public function selecionarEquipe(int $equipeId):array|false
  {
    $query = $this->getQueryBase("e.id = :equipe_id");

    $params = [
      ":acesso_equipes" => $_SESSION["acesso_equipes"],
      ":equipe_id" => $equipeId
    ];

    return $this->executeSQL($query, $params);
  }

  /** 
   * Cria o registro de uma equipe no banco de dados
   * 
   * @param string $nome O Nome da equipe
   * @param int $produtoId o ID do produto
   * @param string $descricao A descrição da equipe
   * 
   * @return bool Se funcionou
   */
  public function criarEquipe(string $nome, int $produtoId, ?string $descricao = null):bool
  {
    $params = [
      ":nome" => $nome,
      ":descricao" => $descricao,
      ":produto_id" => $produtoId,
      ":created" => date($_ENV['DATE_FORMAT'])
    ];

    return $this->insertSQL($this->tabela, $params);
  }

  /**
   * Atualiza o registro no banco de dados atualizando o campo de modified.
   * 
   * @param array $params Os parâmetros do updateSQL
   * @param int $equipeId A row de equipes
   * 
   * @return bool Se funcionou
   */
  public function updateEquipe(array $params, int $equipeId): bool
  {
    $modified = date($_ENV["DATE_FORMAT"]);
    $params[":modified"] = $modified;
    return $this->updateSQL($this->tabela, $params, $equipeId);
  }

  /**
   * Muda o ID do status de uma equipe
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * @param int $statusId O novo ID do status.
   * 
   * @param int $statusId 1 = Desativado
   * @param int $statusId 2 = Pausado
   * @param int $statusId 3 = Ativado
   * 
   * @return bool Se funcionou
   */
  public function mudarStatus(int $equipeId, int $statusId): bool
  {
    $params = [
      ":equipe_status_id" => $statusId
    ];

    return $this->updateEquipe($params, $equipeId);
  }

  /** 
   * Desativa uma equipe 
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return bool Se funcionou
   */
  public function desativar(int $equipeId): bool
  {
    // Não retira os usuários da equipe, já que os usuários dela não devem perder o acesso aos leads.
    return $this->mudarStatus($equipeId, 1);
  }

  /** 
   * Pausa uma equipe 
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return bool Se funcionou
   */
  public function congelar(int $equipeId): bool
  {
    return $this->mudarStatus($equipeId, 2);
  }

  /** 
   * Despausa uma equipe 
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return bool Se funcionou
   */
  public function ativar(int $equipeId): bool
  {
    return $this->mudarStatus($equipeId, 3);
  }

  /**
   * Retorna os usuários que não estão na equipe e estão ativos
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return array|false
   */
  public function eleitosAEquipe(int $equipeId)
  {
    $usuariosQ = <<<SQL
      SELECT 
        u.id, u.nome, nivel_acesso_id
      FROM usuarios u
      WHERE 
        (u.id NOT IN (
          SELECT usuario_id FROM equipes_usuarios WHERE equipe_id = :equipe_id
        )) AND (u.usuario_status_id = 3)
    SQL;

    $params = [
      ":equipe_id" => $equipeId
    ];

    return $this->executeSQL($usuariosQ, $params);
  }

  /**
   * Retorna os usuários que serão os próximos em uma equipe
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return array|false
   */
  public function proximos(int $equipeId)
  {
    $query = <<<SQL
      SELECT 
        vez, usuario_id 
      FROM equipes_usuarios 
      WHERE equipe_id = :equipe_id 
      AND pode_receber_leads = 1
      ORDER BY id ASC 
      LIMIT 3
    SQL;

    $params = [
      ":equipe_id" => $equipeId
    ];

    return $this->executeSQL($query, $params);
  }

  /**
   * Retorna a menor VEZ de uma equipe
   * 
   * @param int $equipeId O ID da equipe a ser editada
   * 
   * @return int
   */
  public function minVez(int $equipeId):int
  {
    $query = <<<SQL
      SELECT
        MIN(vez) AS vez 
      FROM equipes_usuarios 
      WHERE 
        (pode_receber_leads = 1)
        AND (equipe_id = :equipe_id)
      LIMIT 1
    SQL;

    $params = [
      ":equipe_id" => $equipeId
    ];

    $minvez = $this->executeSQL($query, $params, true)[0];
    if ($minvez["vez"] === null) return 0;
    return $minvez["vez"];
  }

  public function vezUsuario($registroId)
  {
    $query = <<<SQL
    SELECT vez 
    FROM equipes_usuarios 
    WHERE 
      id = :id
    LIMIT 1
    SQL;

    $params = [
      ":id" => $registroId
    ];

    $minvez = $this->executeSQL($query, $params, true)[0];
    if ($minvez["vez"] === null) return 0;
    return $minvez["vez"];
  }

  /**
   * Adiciona um usuário em uma equipe com base no $params
   */
  public function adicionarUsuario(array $params):bool
  {
    return $this->insertSQL("equipes_usuarios", $params);
  }

  /**
   * Pega o ID do registro de equipes_usuarios
   */
  public function getIdEquipesUsuarios(int $equipeId, int $usuarioId)
  {
    $query = <<<SQL
      SELECT id
      FROM equipes_usuarios
      WHERE
        equipe_id = :equipe_id
        AND usuario_id = :usuario_id
    SQL;

    $params = [
      ":equipe_id" => $equipeId,
      ":usuario_id" => $usuarioId
    ];

    $array = $this->executeSQL($query, $params);
    return $array[0]["id"];
  }

  /**
   * Troca se o usuário pode receber leads ou não
   */
  public function alterarRecebimento(int $equipeId, int $usuarioId, int $set)
  {
    $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);

    $vez = $this->minVez($equipeId);

    $params = [
      ":vez" => $vez,
      ":pode_receber_leads" => $set  
    ];

    return $this->updateSQL("equipes_usuarios", $params, $registroId);
  }

  public function priorizar(int $equipeId, int $usuarioId)
  {
    $this->mudarVez($equipeId, $usuarioId, 1);
  }

  public function prejudicar(int $equipeId, int $usuarioId)
  {
    $this->mudarVez($equipeId, $usuarioId, -1);
  }

  public function mudarVez(int $equipeId, int $usuarioId, int $set)
  {
    $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);

    $vez = $this->vezUsuario($registroId);
    
    if (($set === -1) && $vez === 0)
      return false;

    $vez = $vez + $set;
    
    $params = [
      ":vez" => $vez
    ];

    return $this->updateSQL("equipes_usuarios", $params, $registroId);
  }

  public function retirarUsuario(int $equipeId, int $usuarioId)
  {
    $registroId = $this->getIdEquipesUsuarios($equipeId, $usuarioId);
    return $this->deleteByIdSQL("equipes_usuarios", $registroId);
  }
}
