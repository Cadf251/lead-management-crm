<?php

use App\adms\Core\AppContainer;
use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\HTMLHelper;

include_once "app/adms/Views/partials/calendar.php";

?>

<h1 class="titulo titulo--1">Olá, <?php echo AppContainer::getAuthUser()->getUsuarioNome() ?></h1>
<h2 class="titulo titulo--3">Selecione um período</h2>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<form method="post" class="inline-icons">
  <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken("form_dashboard"); ?>">
  <input type="text" name="periodo" class="input--date" value="<?php echo isset($this->data["form"]["periodo"]) ? $this->data["form"]["periodo"] : "" ?>" id="myDate">
  <button type="reset" class="small-btn small-btn--gray"><i class="fa-solid fa-rotate"></i></button>
  <button type="submit" class="small-btn small-btn--normal"><i class="fa-solid fa-paper-plane"></i></button>
</form>

<?php
/*
if ($this->data["valido"]) {
  echo <<<HTML
    <p class="descricao">Selecionado: {$this->data["form"]["periodo"]}</p>
    <div class="grafico-card">
  HTML;

  
  if(isset($this->data["por_qualificacao"]))
  echo <<<HTML
  <div class="w5">
    <div class="grafico__header"><h2 class="grafico__titulo">{$this->data["por_qualificacao"]["total"]} Leads</h2></div>
    <div id="chart--pie"></div>
  </div>
  HTML;

  if(isset($this->data["por_periodo"]))
  echo <<<HTML
  <div class="w7">
    <div class="grafico__header"><h2 class="grafico__titulo">{$this->data["por_qualificacao"]["vendas"]} Vendas</h2></div>
    <div id="chart--line"></div>
  </div>
  HTML;

  if(isset($this->data["equipes"])){
    $botaoEquipes = HTMLHelper::renderButtonLink("dashboard-equipes/", "arrow-up-right-from-square", "Dashboard de Equipes");
    echo <<<HTML
    <div class="w6">
      <div class="grafico__header"><h2 class="grafico__titulo">Ranking Equipes</h2> $botaoEquipes</div>
      <div id="chart--ranking-equipes"></div>
    </div>
    HTML;
  }

  if(isset($this->data["usuarios"])){
    $botaoUsuarios = HTMLHelper::renderButtonLink("dashboard-usuarios/", "arrow-up-right-from-square", "Dashboard de Usuários");
    echo <<<HTML
    <div class="w6">
      <div class="grafico__header"><h2 class="grafico__titulo">Ranking Usuários</h2> $botaoUsuarios</div>
      <div id="chart--ranking-vendedores"></div>
    </div>
    HTML;
  }

  echo <<<HTML
  </div>
  HTML;
}
?>

<script>
  // Criar gráfico do total de leads
  function graficoPie() {
    var options = {
      series: [<?php echo $this->data["por_qualificacao"]["series"] ?>],
      chart: {
        width: 380,
        type: 'pie',
      },
      labels: [<?php echo $this->data["por_qualificacao"]["labels"] ?>],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var chart = new ApexCharts(document.querySelector("#chart--pie"), options);
    chart.render();
  }

  // Criar gráfico do total de leads por periodo
  function graficoLine() {
    var options = {
      series: [{
        name: "Leads",
        type: 'line',
        data: [<?php echo $this->data["por_periodo"]["series"] ?>]
      }, {
        name: "Vendas",
        type: 'line',
        data: [<?php echo $this->data["por_periodo"]["series_vendas"] ?>]
      }],
      chart: {
        height: 350,
        zoom: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight',
      },
      title: {
        text: 'Leads/vendas por <?php echo $this->data["por_periodo"]["metodo"] ?>',
        align: 'left'
      },
      grid: {
        row: {
          colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
          opacity: 0.5
        },
      },
      yaxis: [{
          title: {
            text: 'Leads'
          }
        },
        {
          opposite: true,
          title: {
            text: 'Vendas (R$)'
          }
        }
      ],
      xaxis: {
        categories: [<?php echo $this->data["por_periodo"]["labels"] ?>],
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart--line"), options);
    chart.render();
  }

  function graficoBar() {
    var options = {
      series: [{
        data: [<?php echo $this->data["equipes"]["comissoes"] ?>]
      }],
      chart: {
        type: 'bar',
        height: 350
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          borderRadiusApplication: 'end',
          barHeight: 30,
          horizontal: true,
        }
      },
      dataLabels: {
        enabled: false
      },
      xaxis: {
        categories: [<?php echo $this->data["equipes"]["nomes"] ?>],
      },
      tooltip: {
        custom: function({
          series,
          seriesIndex,
          dataPointIndex,
          w
        }) {
          const equipesInfo = [
            <?php
            foreach ($this->data["equipes"]["info"] as $info) {
              echo <<<JS
                  { desc: "{$info['descricao']}", produto: "{$info['produto']}"},
                JS;
            }
            ?>
          ];
          const equipe = equipesInfo[dataPointIndex];
          return `
            <div style="padding:8px">
              <b>${w.globals.labels[dataPointIndex]}</b><br/>
              Comissão: ${series[seriesIndex][dataPointIndex]}<br/>
              ${equipe.desc}<br/>
              Produto: <i>${equipe.produto}</i>
            </div>
          `;
        }
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart--ranking-equipes"), options);
    chart.render();
  }

  function graficoVendedores() {
    var options = {
      series: [
        <?php
        foreach ($this->data["usuarios"]["dados"] as $dado) {
          echo "
              {
                name: \"{$dado['nome']}\",
                data: [{$dado['data']}]
              },
            ";
        }
        ?>
      ],
      chart: {
        type: 'bar',
        height: 350,
        stacked: true,
      },
      plotOptions: {
        bar: {
          barHeight: 30,
          horizontal: true,
          dataLabels: {
            total: {
              enabled: true,
              offsetX: 0,
              style: {
                fontSize: '13px',
                fontWeight: 900
              }
            }
          }
        },
      },
      stroke: {
        width: 1,
        colors: ['#fff']
      },
      xaxis: {
        categories: [<?php echo $this->data["usuarios"]["linhas"] ?>],
        labels: {
          formatter: function(val) {
            return "R$"+val
          }
        }
      },
      yaxis: {
        title: {
          text: undefined
        },
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return "R$"+val
          }
        }
      },
      fill: {
        opacity: 1
      },
      legend: {
        position: 'top',
        horizontalAlign: 'left',
        offsetX: 40
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart--ranking-vendedores"), options);
    chart.render();
  }
  <?php
  if($this->data["valido"]) {
    if(isset($this->data["por_qualificacao"]))
    echo <<<JS
    graficoLine();
    JS;

    if(isset($this->data["por_periodo"]))
    echo <<<JS
    graficoPie();
    JS;

    if(isset($this->data["equipes"]))
    echo <<<JS
    graficoBar();
    JS;

    if(isset($this->data["usuarios"]))
    echo <<<JS
    graficoVendedores();
    JS;
  }

  ?>
</script>*/