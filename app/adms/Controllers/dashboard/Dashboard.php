<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\HTMLHelper;
use App\adms\Models\Repositories\DashboardRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Views\Services\LoadViewService;
use DateTime;

class Dashboard
{
  /** @var array|string|null $data Valores enviados para a VIEW */
  private array|string|null $data = null;

  /** @var object|null $conexao A conexão com o banco do cliente */
  private object|null $conexao = null;

  public function index(string $tempo)
  {
    // Recebe o período
    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Verifica se o POST corresponde ao formulário
    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_dashboard", $this->data["form"]["csrf_token"])) {
      // Se tiver OK, inicia o respositório e passa os dados para o data
      $tempo = $this->tratarTempo();
      if ($tempo !== false)
        $this->repository($tempo);
    }
    $this->data["title"] = "Dashboard";

    // Carrega a VIEW
    $loadView = new LoadViewService("adms/Views/dashboard/dashboard", $this->data);
    $loadView->loadView();
  }

  /**
   * Tenta tratar o tempo e tranformar em SQL.
   */
  public function tratarTempo()
  {
    // Recebe e simplifica o tempo
    $tempo = $this->data["form"]["periodo"];

    if (empty($tempo))
      return false;

    // Se a consulta for de apenas um dia, adapta a string
    if (strpos($tempo, "até") === false)
      $tempo = "$tempo até $tempo";

    $tempo = str_replace(" até ", "_", $tempo);
    $tempoArray = explode("_", $tempo);

    foreach ($tempoArray as $key => $value) {
      $date = DateTime::createFromFormat('d/m/Y', $value);
      $tempoObject[$key] = $date;
      $tempoArray[$key] = $date->format('Y-m-d');
    }

    // Calcula a diferença entre os dois
    $dias = ($tempoObject[1]->getTimestamp() - $tempoObject[0]->getTimestamp()) / (60 * 60 * 24);
    $dias = (int) $dias;

    // Adiciona 1 dia
    $dias++;

    // Seta os valores
    $this->data["diferenca_dias"] = $dias;
    $this->data["tempo_query"] = "BETWEEN '{$tempoArray[0]} 00:00:00' AND '{$tempoArray[1]} 23:23:59'";
  }

  /**
   * Iniciar o repositório
   * 
   */
  public function repository()
  {
    // Cria a conexão
    $conexao = new DbConnectionClient(null);
    $this->conexao = $conexao->conexao;

    // Instancia o repository
    $data = [
      "diferenca_dias" => $this->data["diferenca_dias"],
      "tempo_query" => $this->data["tempo_query"]
    ];

    $repository = new DashboardRepository($data, $this->conexao);

    // Cria o grafico total
    $arrayTotal = $repository->leadsTotal();
    $this->data["por_qualificacao"] = $this->arrangeLeadsArray($arrayTotal);
    $arrayPeriodo = $repository->leadPeriodo();
    $this->data["por_periodo"] = $this->arrangeLeadsPorPeriodo($arrayPeriodo);
  }

  /**
   * Converte os dados recebidos do Repository para um array separado pela qualificação do lead
   * 
   */
  public function arrangeLeadsArray(array $leads): array
  {
    $leadsCount = [
      "'Não responde(m)'" => 0,
      "'Desqualificado(s)'" => 0,
      "'Qualificado(s)'" => 0,
      "'Oportunidade(s)'" => 0,
      "'Contratado(s)'" => 0
    ];

    $total = 0;

    foreach ($leads as $lead) {
      if ($lead["lead_status_id"] === 5) {
        $leadsCount["'Contratado(s)'"] += $lead["total_leads"];
      }

      if ($lead["lead_status_id"] === 4) {
        $leadsCount["'Oportunidade(s)'"] += $lead["total_leads"];
      }

      if ($lead["lead_status_id"] === 3) {
        $leadsCount["'Qualificado(s)'"] += $lead["total_leads"];
      }

      if ($lead["lead_status_id"] === 2) {
        $leadsCount["'Desqualificado(s)'"] += $lead["total_leads"];
      }
      if ($lead["lead_status_id"] === 1) {
        $leadsCount["'Não responde(m)'"] += $lead["total_leads"];
      }

      $total += $lead["total_leads"];
    }

    $keys = array_keys($leadsCount);
    $labels = implode(", ", $keys);
    $series = implode(", ", $leadsCount);

    return [
      "total" => $total,
      "labels" => $labels,
      "series" => $series
    ];
  }

  /**
   * Converte um array de leads por periodo
   */
  public function arrangeLeadsPorPeriodo(array $leads):array
  {
    $resumido = $leads[0];
    $array = [];

    if ($leads["metodo"] === "mês")
      $dataFormat = "m/Y";
    else 
      $dataFormat = "d/m/Y";

    foreach ($resumido as $linha){
      $data = date($dataFormat, strtotime($linha["periodos"]));
      $array["'{$data}'"] = $linha["total_leads"];
    }

    $keys = array_keys($array);
    $labels = implode(", ", $keys);
    $series = implode(", ", $array);

    return [
      "metodo" => $leads["metodo"],
      "labels" => $labels,
      "series" => $series
    ];
  }

  /** DESABILITAR
   * 
   * Está função deveria estar na VIEW.
   * 
   * Mesmo assim, os gráficos serão subtituidos por uma API ou biblioteca de gráficos.
   * 
   */
  public function graficoFinal(array $leadsCount, string $graficoTitle = "Total")
  {
    // Define a ordem ortodoxa das colunas
    $ordem = [
      "Total",
      "Não respondem",
      "Desqualificado(s)",
      "Qualificado(s)",
      "Oportunidade(s)",
      "Contratado(s)"
    ];

    foreach ($ordem as $o) {
      if (!isset($leadsCount[$o]))
        $leadsCount[$o] = 0;
    }

    $qualificados = ($leadsCount["Contratado(s)"] + $leadsCount["Oportunidade(s)"] + $leadsCount["Qualificado(s)"]);

    $qualificacao = $this->calcularPercentual($qualificados, $leadsCount["Total"]);
    $contratacao = $this->calcularPercentual($leadsCount["Contratado(s)"], $leadsCount["Total"]);

    $label = <<<HTML
      <div class="porcentagem-container">
        <div><p class="porcentagem">$qualificacao</p><span>De qualificação</span></div>
        <div><p class="porcentagem">$contratacao</p><span>De contratação</span></div>
      </div>
    HTML;

    $leadsRows = [];

    foreach ($ordem as $o) {
      $leadsRows[] = [
        "valor" => $leadsCount[$o],
        "label" => $o
      ];
    }

    return HTMLHelper::renderGraficoCompleto($graficoTitle, $leadsRows, $label);
  }

  /** Calcula a porcentagem e retorna o número formatado em string */
  public function calcularPercentual($parte, $total) {
    if ($total == 0 || $parte == 0)
      return "0,00%";
    $int = ($parte / $total) * 100;
    return number_format($int, 2, ",")."%";
  }
}
