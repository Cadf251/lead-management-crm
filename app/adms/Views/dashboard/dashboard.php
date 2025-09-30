<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\HTMLHelper;

include_once "app/adms/Views/partials/calendar.php";
?>
<h1 class="titulo-1 w12">Olá, <?php echo $_SESSION["usuario_nome"] ?></h1>
<h2 class="titulo-3">Selecione um período</h2>

<form method="post" class="inline-icons">
  <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken("form_dashboard"); ?>">
  <input type="text" name="periodo" class="input--date" value="<?php echo isset($this->data["form"]["periodo"]) ? $this->data["form"]["periodo"] : "" ?>" id="myDate">
  <button type="reset" class="small-btn small-btn--gray"><i class="fa-solid fa-rotate"></i></button>
  <button type="submit" class="small-btn small-btn--normal"><i class="fa-solid fa-paper-plane"></i></button>
</form>

<?php
if(isset($this->data["por_qualificacao"])){
  echo <<<HTML
    <p class="descricao">Selecionado: {$this->data["form"]["periodo"]}</p>
  HTML;
}
?>

<?php 
if(isset($this->data["por_qualificacao"])){
  echo <<<HTML
    <div class="grafico-card">
      <div class="grafico__container">
        <div id="chart--pie"></div>
      </div>
      <div class="grafico__container">
        <div id="chart--line"></div>
      </div>
      <div class="grafico__container">Vendas Total</div>
      <div class="grafico__container">Vendas linechart</div>
      <div class="grafico__container">Por equipe ranking</div>
      <div class="grafico__container">Vendedores ranking</div>
    </div>
  HTML;
}
?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  // Cria o calendário
  flatpickr("#myDate", {
    dateFormat: "d/m/Y",
    maxDate: "today",
    mode: "range",
    theme: "default",
    locale: "pt"
  });

  // Criar gráfico do total de leads
  function graficoPie(){
    var options = {
      series: [<?php echo $this->data["por_qualificacao"]["series"] ?>],
      chart: {
        width: 380,
        type: 'pie',
      },
      labels: [<?php echo $this->data["por_qualificacao"]["labels"] ?>],
      title: {
        text: "Total de leads: <?php echo $this->data["por_qualificacao"]["total"] ?>",
        align: 'left'
      },
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
  function graficoLine(){
    var options = {
      series: [{
        name: "Leads",
        data: [<?php echo $this->data["por_periodo"]["series"] ?>]
      }],
      chart: {
        height: 350,
        type: 'line',
        zoom: {
          enabled: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'straight'
      },
      title: {
        text: 'Leads por <?php echo $this->data["por_periodo"]["metodo"] ?>',
        align: 'left'
      },
      grid: {
        row: {
          colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
          opacity: 0.5
        },
      },
      xaxis: {
        categories: [<?php echo $this->data["por_periodo"]["labels"] ?>],
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart--line"), options);
    chart.render();
  }
  <?php
  if(isset($this->data["por_qualificacao"])){
    echo <<<JS
      graficoLine();
      graficoPie();
    JS;
  }
  ?>
</script>