<?php

namespace App\adms\Models\Repositories;

use App\adms\Models\Services\DbOperations;

/**
 * Repositório para criar o dashboard
 */
class DashboardRepository extends DbOperations
{
  /**
   * Constrói o $tempo personalizado
   */
  public function __construct(public array $data, public object|null $conexao){
    parent::__construct($conexao);
  }

  /**
   * Retorna a query de leads
   * 
   * @param string $where As condições WHERE | Deve conter necessariamente o periodo do created | Pode filtrar por equipes, campanhas, vendedores, etc.
   * @param int $metodo Recebe o método de agrupamento
   * @return string SQL
   */
  public function leadsQuery(string $where, int $metodo):string
  {
    // Cria um array para definir qual será o método de agrupamento
    $metodos = [
      0,  // Por status
      1,  // Por dia
      2,  // Por semana
      3   // Por mês
    ];

    // Verifica se o método existe
    if (!in_array($metodo, $metodos))
      // Cria um log de erro melhor
      return "";

    // Cria partes da query com base no método de agrupamento
    switch($metodo){
      case 0:
        $select = <<<SQL
          l.lead_status_id,
          ls.nome AS status_nome
        SQL;
        $group = <<<SQL
          GROUP BY lead_status_id
        SQL;
        $order = "";
        break;
      case 1:
        $select = <<<SQL
          DATE(a.created) AS periodos
        SQL;
        $group = <<<SQL
          GROUP BY DATE(a.created)
        SQL;
        $order = <<<SQL
          ORDER BY periodos
        SQL;
        break;
      case 2:
        $select = <<<SQL
          YEARWEEK(a.created, 1) AS ano_semana, -- 1 força a semana começar na segunda
          STR_TO_DATE(CONCAT(YEARWEEK(a.created, 1), ' Monday'), '%X%V %W') AS periodos
        SQL;
        $group = <<<SQL
          GROUP BY YEARWEEK(a.created, 1)
        SQL;
        $order = <<<SQL
          ORDER BY ano_semana
        SQL;
        break;
      case 3:
        $select = <<<SQL
          DATE_FORMAT(a.created, '%Y-%m') AS periodos
        SQL;
        $group = <<<SQL
          GROUP BY DATE_FORMAT(a.created, '%Y-%m')
        SQL;
        $order = <<<SQL
          ORDER BY periodos
        SQL;
        break;
    }

    // Verifica as permissões
    if (!in_array(3, $_SESSION["permissoes"])){
      if (in_array(4, $_SESSION["permissoes"])){
        $where .= <<<SQL
        AND (
          (e.id IN (:acesso_equipes))
          OR (a.usuario_id = :usuario_id)
        )
        SQL;
      } else {
        $where .= <<<SQL
          AND (a.usuario_id = :usuario_id)
        SQL;
      }
    }

    return <<<SQL
      SELECT
        COUNT(DISTINCT a.lead_id) AS total_leads,
        $select
      FROM atendimentos a
      INNER JOIN leads l ON a.lead_id = l.id
      INNER JOIN lead_status ls ON l.lead_status_id = ls.id
      INNER JOIN equipes e ON a.equipe_id = e.id
      WHERE 
        $where
      $group
      $order
    SQL;
  }

  /**
   * Executa a query de leads com um método específico
   */
  public function executarQueryLeads(string $where, int $metodo):array|bool
  {
    $whereBase = <<<SQL
      (a.created {$this->data["tempo_query"]})
    SQL;

    $whereFinal = <<<SQL
      $whereBase
      $where
    SQL;

    $query = $this->leadsQuery($whereFinal, $metodo);

    $params = [
      ":usuario_id" => $_SESSION["usuario_id"],
      "acesso_equipes" => $_SESSION["acesso_equipes"] ?? ""
    ];

    return $this->executeSQL($query, $params);
  }

  /**
   * Gera um array de leads para o gráfico total
   */
  public function leadsTotal():array|bool
  {
    return $this->executarQueryLeads("", 0);
  }

  public function leadPeriodo():array|bool
  {
    if ($this->data["diferenca_dias"] < 14){
      $metodo = 1;
      $nome = "dia";
    }
    else if ($this->data["diferenca_dias"] < 60){
      $metodo = 2;
      $nome = "semana";
    } else { 
      $metodo = 3;
      $nome = "mês";
    }

    return [
      "metodo" => $nome,
      $this->executarQueryLeads("", $metodo)
    ];
  }

  /** Retorna todas as equipes ativas */
  public function equipes()
  {

  }
}