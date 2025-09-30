<?php

namespace App\adms\Helpers;

/**
 * Cria estruturas limpas de HTML com facilidade
 */
class HTMLHelper
{
  /** Cria o link da NAV
   * 
   * @param string $href O link HREF do botão
   * @param string $icon A terminação da segunda class do ícone, exemplos: fa-{$icon}
   * @param string $label O placeholder do link
   * 
   * @return string a estrutura completa: a.nav__link>i+span
   */
  public static function renderNavLink(string $href, string $icon, string $label) :string
  {
    return <<<HTML
      <a href="{$_ENV['HOST_BASE']}$href" class="nav__link"><i class="fa-solid fa-$icon"></i> <span class="nav__texto">$label</span></a>
    HTML;
  }

  /**
   * Cria um CARD com um conteúdo qualquer.
   * 
   * @param string $content O conteúdo que vai dentro do card
   * @param array $classes As classes adicionais do card
   * 
   * @return string HTML
   */
  public static function renderCard(string $content, array $classes = []) :string
  {
    if(!empty($classes))
      $classes = " ".implode(" ", $classes);
    else 
      $classes = "";
    return <<<HTML
      <div class="card-padrao {$classes}">
        $content
      </div>
    HTML;
  }

  /**
   * Configura um título 3 e chama a renderCard
   * 
   * @param string $title O título
   * @param string $content O conteúdo em baixo do título
   * @param array Um array de classes adicionais do card
   * 
   * @return string HTML
   */
  public static function renderCardWTitle(string $title, string $content, array $classes = []):string
  {
    $content = <<<HTML
      <h3 class="titulo-3">$title</h3>
      $content
    HTML;
    return HTMLHelper::renderCard($content, $classes);
  }

  /**
   * Cria um título com diversos ícones inline
   * 
   * @param string $title O título
   * @param string $icons Os ícones em HTML
   * 
   * @return string HTML
   */
  public static function renderTitleWIcons(string $title, string $icons):string
  {
    return <<<HTML
      <div class="card__icons">
        <h3 class="titulo-3">$title</h3>
        $icons
      </div>
    HTML;
  }

  /**
   * Cria um card completo, com título, descrição, e ícones inline com o título
   * 
   * @param string $title O título
   * @param string $content O conteúdo depois da descrição
   * @param string $descricao A descrição que será escrita em cinza
   * @param string $icons Os ícones em HTML
   * 
   * @return string HTML
   */
  public static function renderCardComplete(string $title, string $content, string $descricao, string $icons):string
  {
    $header = HTMLHelper::renderTitleWIcons($title, $icons);

    $contentFinal = <<<HTML
      $header
      <p class="descricao">$descricao</p>
      $content
    HTML;

    return HTMLHelper::renderCard($contentFinal);
  }

  /**
   * Rende uma tabela em um container
   * 
   * @param string $title O título
   * @param string $header O header th
   * @param string $rows As linhas tr
   * 
   * @return string HTML
   */
  public static function renderTable(string $title, string $header, string $rows):string
  {
    return <<<HTML
      <b>$title</b>
      <div class="table-container">
        <table class="table-container__table">
          $header
          $rows
        </table>
      </div>
    HTML;
  }

  /**
   * Cria um gráfico de colunas
   * 
   * @param string $title O título do gráfico
   * @param string $content O conteúdo já preparado com as colunas
   * 
   * @return string HTML
   */
  public static function renderGrafico(string $title, string $content) :string
  {
    return <<<HTML
      <div class="grafico">
        <b class="grafico__title">$title</b>
        <div class="grafico__box">
          $content
        </div>
      </div>
    HTML;
  }

  /**
   * Cria com um array as colunas para um gráfico
   * 
   * @param array $rows = ["valor" => x, "label" => "Nome_Parâmetro"]
   * 
   * @return string HTML
   */
  public static function renderColunas(array $rows):string
  {
    $final = '';
    foreach ($rows as $row) {
      $final .= <<<HTML
        <div class="grafico__coluna">
          <div class="grafico__valor">{$row["valor"]}</div>
          <div class="grafico__barra" style="height: {$row['valor']}%;"></div>
          <div class="grafico__label">{$row['label']}</div>
        </div>
      HTML;
    }
    return $final;
  }

  /**
   * Use as funções renderColunas e renderGrafico
   * Monta as colunas usando o array rows
   * 
   * @param string $title O título do gráfico
   * @param array $rows = ["valor" => x, "label" => "Nome_Parâmetro"]
   * @param string $label Adiciona um label depois das colunas
   * 
   * @return string HTML O gráfico completo
   */
  public static function renderGraficoCompleto(string $title, array $rows, string $label = '')
  {
    $contentM = HTMLHelper::renderColunas($rows);
    $content = $contentM . $label;
    return HTMLHelper::renderGrafico($title, $content);
  }

  /**
   * Cria um formulário padrão
   * 
   * @param string $content O conteúdo com os labels e inputs
   * @param string $action O placeholder do botão submit
   * @param array $formClasses As classes adicionais do formulário
   * @param array $cardClasses As classes adicionais do card
   */
  public static function renderForm(string $content, string $action, array $formClasses = [], array $cardClasses = []):string
  {
    if (!empty($formClasses))
      $formClasses = implode(" ", $formClasses);
    else 
      $formClasses = "";

    $form = <<<HTML
      <form class="form-padrao {$formClasses}" method="post">
        $content
        <button type="submit" class="small-btn small-btn--normal">$action</button>
      </form>
    HTML;
    return HTMLHelper::renderCard($form, $cardClasses);
  }

  /**
   * Cria um formulário padrão com um título
   * 
   * @param string $title O título do formulário
   * @param string $content O conteúdo com os labels e inputs
   * @param string $action O placeholder do botão submit
   * @param array $formClasses As classes adicionais do formulário
   * @param array $cardClasses As classes adicionais do card
   */
  public static function renderFormWTitle(string $title, string $content, string $action, array $formClasses = [], array $cardClasses = []):string
  {
    $contentFinal = <<<HTML
      <h2 class="titulo-2">$title</h2>
      $content
    HTML;
    return HTMLHelper::renderForm($contentFinal, $action, $formClasses, $cardClasses);
  }

  /**
   * Cria um formulário mais fino
   * 
   * @param string $title O título do gráfico
   * @param string $content O conteúdo do form
   * @param string $action O conteúdo do botão submit
   * 
   * @return string HTML renderFormWTitle com classes adicionais
   */
  public static function thinnerForm($title, $content, $action)
  {
    return HTMLHelper::renderFormWTitle($title, $content, $action, ['form-padrao--thinner'], ['card-padrao--thinner']);
  }

  /**
   * Cria um menu que vai no topo da página
   * 
   * Exemplo: Dashboard e Leads
   * 
   * @param array $rows O array contendo a informação de cada botão do menu, no formato:
   * ["href", "class", "text"], [...]
   * @param string $label O nome do Menu, que precede os botões
   * @param string $title Um título h2 opcional
   */
  public static function renderTopSelection(array $rows, string $label, string $title = '')
  {
    $rowFinal = '';
    foreach ($rows as $row) {
      $rowFinal .= <<<HTML
        <a href="{$_ENV['HOST_BASE']}{$row['href']}" class="top-selection__link {$row['class']}">{$row["text"]}</a>
      HTML;
    }

    return <<<HTML
      <div class='header-buttons'>
        <h2 class='titulo-2'>$title</h2>
        <div class="top-selection">
          $label
          $rowFinal
        </div>
      </div>
    HTML;
  }

  /**
   * Retona o cabeçalho com um botão
   * 
   * @var string $title O título que aparecerá no H2
   * @var string $mouseover O title do botão
   * @var string $icon A class do icone
   * 
   * @return string HTML
   */
  public static function renderHeader(string $title, string $href, string $mouseover, string $icon)
  {
    return <<<HTML
      <div class="page-header">
        <a href="$href" class="white-btn" title="{$mouseover}">
          <i class="fa-solid fa-{$icon}"></i>
        </a>
        <h2 class="titulo-2">$title</h2>
      </div>
    HTML;
  }

  /**
   * Cria um link em uma page-header para voltar a página indicada.
   */
  public static function renderVoltar(string $title, string $href)
  {
    return <<<HTML
      <div class="page-header">
        <a class="white-btn centered" href="{$href}" title="Voltar">
          <i class="fa-solid fa-left-long"></i>
        </a>
        <h1 class="titulo-2">$title</h1>
      </div>
    HTML;
  }

  /**
   * Criar um botão que funciona na base do AJAX
   */
  public static function renderButtonAjax($function, $color, $icon, $mouseover)
  {
    return <<<HTML
      <button 
        onclick='{$function}'
        type="button"
        class="small-btn small-btn--{$color}">
        <i class="fa-solid fa-{$icon}" title="{$mouseover}"></i>
      </button>
    HTML;
  }

  /**
   * Cria um botão com um formulário
   */
  public static function renderButtonForm($task, string $icon, string $mouseover, $inputs, $color = "normal")
  {
    return <<<HTML
      <form method="post">
        <input type="hidden" name="task" value="$task">
        $inputs
        <button type="submit" class="small-btn small-btn--$color" title="$mouseover">
          <i class="fa-solid fa-{$icon}"></i>
        </button>
      </form>
    HTML;
  }

  /**
   * Cria um link que é aparentemente igual ao renderButtonForm e o ButtonAjax.
   */
  public static function renderButtonLink(string $href, string $icon, string $mouseover, string  $color = "normal") :string
  {
    return <<<HTML
      <a href="$href" class="small-btn small-btn--$color" title="$mouseover">
        <i class="fa-solid fa-{$icon}"></i>
      </a>
    HTML;
  }

  function renderStatusCard($emoji, $status, $color = "gray")
  {
    return <<<HTML
      <div class="status-card status-card--$color">
        $emoji $status
      </div>
    HTML;
  }

  function dividedBlock($title, $left, $right)
  {
    return <<<HTML
      <h3 class="titulo-3">$title</h3>
      <div class="centered baseline fix">
        <div class="block-container block-container--left">
          $left
        </div>
        <div class="block-container block-container--right">
          $right
        </div>
      </div>
    HTML;
  }
}
