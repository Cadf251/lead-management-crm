<?php

namespace App\adms\Controllers\dashboard;

use App\adms\Helpers\CSRFHelper;
use App\adms\Repositories\DashboardRepository;
use App\adms\Database\DbConnectionClient;
use App\adms\Core\LoadView;
use DateTime;

class Dashboard
{
  /** @var array|string|null $data Valores enviados para a VIEW */
  private array|string|null $data = null;

  /** @var object|null $conexao A conexão com o banco do cliente */
  private object|null $conexao = null;

  public function index(string $tempo):void
  {
    // Recebe o período
    $this->data["form"] = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    $this->data["valido"] = false;

    // Verifica se o POST corresponde ao formulário
    if (isset($this->data["form"]["csrf_token"]) && CSRFHelper::validateCSRFToken("form_dashboard", $this->data["form"]["csrf_token"])) {
      // Se tiver OK, inicia o respositório e passa os dados para o data
      $this->data["valido"] = true;
      $tempo = $this->tratarTempo();

      if ($tempo !== false)
        $this->repository();
    }
    $this->data["title"] = "Dashboard";

    // Carrega a VIEW
    $loadView = new LoadView("adms/Views/dashboard/provisorio", $this->data);
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
   * Iniciar o repositório e passa os valores para o data
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

    // $repository = new DashboardRepository($data, $this->conexao);

    // Adiciona no data os valores do repositório
    // $arrayTotal = $repository->leadsTotal();
    // $this->data["por_qualificacao"] = $this->arrangeLeadsArray($arrayTotal);
    // $arrayPeriodo = $repository->leadPeriodo();
    // $this->data["por_periodo"] = $this->arrangeLeadsPorPeriodo($arrayPeriodo);
    // $arrayEquipes = $repository->equipes();

    // Visível apenas para adms
    // if (in_array(3, $_SESSION["permissoes"]) || (in_array(4, $_SESSION["permissoes"]))){
    //   $this->data["equipes"] = $this->arrangeEquipes($arrayEquipes);
    //   $arrayUsuarios = $repository->usuarios();
    //   $this->data["usuarios"] = $this->arrangeUsuarios($arrayUsuarios);
    // }
  }

  /**
   * Converte os dados recebidos do Repository para um array separado pela qualificação do lead
   * 
   */
  public function arrangeLeadsArray(array $leads): array
  {
    $leadsCount = [
      "'Status indefinido'" => 0,
      "'Não responde(m)'" => 0,
      "'Desqualificado(s)'" => 0,
      "'Qualificado(s)'" => 0,
      "'Oportunidade(s)'" => 0,
      "'Contratado(s)'" => 0
    ];

    $total = 0;
    $vendas = 0.0;

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

      if ($lead["lead_status_id"] === null) {
        $leadsCount["'Status indefinido'"] += $lead["total_leads"];
      }

      $total += $lead["total_leads"];
      $vendas += (float)$lead["comissao"];
    }

    $keys = array_keys($leadsCount);
    $labels = implode(", ", $keys);
    $series = implode(", ", $leadsCount);
    $vendas = "R$ " . number_format($vendas, 2, ",", ".");

    return [
      "total" => $total,
      "labels" => $labels,
      "series" => $series,
      "vendas" => $vendas
    ];
  }

  /**
   * Converte um array de leads por periodo
   */
  public function arrangeLeadsPorPeriodo(array $leads): array
  {
    $resumido = $leads[0];
    $array = [];
    $vendas = [];

    if ($leads["metodo"] === "mês")
      $dataFormat = "m/Y";
    else
      $dataFormat = "d/m/Y";

    foreach ($resumido as $linha) {
      $data = date($dataFormat, strtotime($linha["periodos"]));
      $array["'{$data}'"] = $linha["total_leads"];

      $linha["comissao"] = $linha["comissao"] === null ? 0 : (float)$linha["comissao"];
      $vendas["'{$data}'"] = $linha["comissao"];
    }

    $keys = array_keys($array);
    $labels = implode(", ", $keys);
    $series = implode(", ", $array);
    $seriesVendas = implode(", ", $vendas);

    return [
      "metodo" => $leads["metodo"],
      "labels" => $labels,
      "series" => $series,
      "series_vendas" => $seriesVendas
    ];
  }

  /** Formata o array de equipes */
  public function arrangeEquipes(array $equipes)
  {
    $nomes = [];
    $comissoes = [];
    $info = [];
    for ($i = 0; $i < count($equipes); $i++) {
      switch ($i) {
        case 0:
          $medalha = "🥇 ";
          break;
        case 1:
          $medalha = "🥈 ";
          break;
        case 2:
          $medalha = "🥉 ";
          break;
        default:
          $medalha = "";
          break;
      }
      $nomes[] = "'$medalha{$equipes[$i]["e_nome"]}'";
      $comissoes[] = (float)$equipes[$i]["comissao"] ?? 0;
      $info[] = [
        'descricao' => $equipes[$i]["e_descricao"],
        'produto' => $equipes[$i]["prod_nome"],
        'proposta' => (int)$equipes[$i]["propostas"]
      ];
    }

    $nomes = implode(", ", $nomes);
    $comissoes = implode(", ", $comissoes);

    return [
      "nomes" => $nomes,
      "comissoes" => $comissoes,
      "info" => $info
    ];
  }

  /** Organiza o array de usuários de forma clara e eficiente */
  public function arrangeUsuarios(array $usuarios)
  {
    $index = [];
    $totalComissao = [];
    $produtos = [];
    $nomes = [];

    // Monta índice principal e acumula totais por usuário
    foreach ($usuarios as $usuario) {
      $produto = $usuario["prod_nome"];
      $nome = $usuario["u_nome"];
      $produtos[$produto] = true; // usamos como set
      $nomes[$nome] = true;

      $index[$produto][$nome] = [
        "propostas" => (int) $usuario["propostas"],
        "comissao" => (float) $usuario["comissao"]
      ];

      $totalComissao[$nome] = ($totalComissao[$nome] ?? 0) + (float) $usuario["comissao"];
    }

    // Preenche usuários faltantes em cada produto
    foreach ($produtos as $produto => $_) {
      foreach ($nomes as $nome => $_) {
        if (!isset($index[$produto][$nome])) {
          $index[$produto][$nome] = [
            "propostas" => 0,
            "comissao" => 0
          ];
        }
      }
    }

    // Ordena nomes pelo total de comissão
    arsort($totalComissao);
    $nomesOrdenado = array_map(fn($n) => "'$n'", array_keys($totalComissao));

    // Prepara dados finais por produto
    $final = [];
    foreach (array_keys($produtos) as $produto) {
      $seq = [];
      foreach ($nomesOrdenado as $nome) {
        $nomeSemAspas = trim($nome, "'");
        $seq[] = $index[$produto][$nomeSemAspas]["comissao"];
      }

      $final[] = [
        "nome" => $produto,
        "data" => implode(", ", $seq)
      ];
    }

    return [
      "dados" => $final,
      "linhas" => implode(", ", $nomesOrdenado)
    ];
  }
}