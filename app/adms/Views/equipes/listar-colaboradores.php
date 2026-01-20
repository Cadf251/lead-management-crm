<?php

use App\adms\UI\Button;
use App\adms\UI\Header;
use App\adms\UI\Table;

$equipe = $this->data["equipes"][0];

$colaboradores = $equipe["colaboradores"];

$proximos = $equipe["proximos"];

// HEADER
$voltar = Button::create("Voltar")
  ->color("blue")
  ->withIcon("back")
  ->link("equipes/")
  ->render();

$button = Button::create("+ Novo Colaborador")
  ->color("black")
  ->data([
    "action" => "action:core",
    "url" => "colaboradores/novo/{$equipe['id']}",
    "action-type" => "overlay"
  ]);

$header = Header::create("Colaboradores | {$equipe["nome"]}")
  ->addButton($voltar)
  ->addButton($button)
  ->addBadge($equipe["status_badge"])
  ->withDescription($equipe["descricao"])
  ->render();

echo $header;

// INFOBOX
echo <<<HTML
<div class="js--infobox">
{$equipe["fila"]["infobox"]}
</div>
HTML;

// Começa a criar a tabela de usuários
$tableHeader = <<<HTML
<tr>
  <th>Usuários {$equipe["numero_badge"]}</th>
  <th class="cell-centered">Função</th>
  <th class="cell-centered">Recebe leads?</th>
  <th class="cell-centered">Priorizar/prejudicar</th>
  <th class="cell-centered">Remover da equipe</th>
</tr>
HTML;

$rows = "";

if (empty($colaboradores)) {
  $rows .= <<<HTML
  <tr>
    <td colspan="6">Nenhum usuário nessa equipe</td>
  </tr>
  HTML;
} else {
  foreach ($colaboradores as $colaborador) {
    $rows .= <<<HTML
    <tr>
      <td>{$colaborador["usuario_nome"]}</td>
      <td>{$colaborador["funcao_select"]}</td>
      <td class="cell-centered">{$colaborador["recebe_leads_switch"]}</td>
      <td class="cell-centered">{$colaborador["vez_buttons"]}</td>
      <td class="cell-centered">{$colaborador["remover_button"]}</td>
    </tr>
    HTML;
  }
}

$table = Table::create($tableHeader, "table--colaboradores")
  ->withTitle("Usuários")
  ->addRows($rows)
  ->render();

echo $table;