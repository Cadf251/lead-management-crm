<?php

use App\adms\UI\Button;
use App\adms\UI\Card;
use App\adms\UI\Header;
use App\adms\UI\Table;

$equipe = $this->data["equipes"][0];

$colaboradores = $equipe["colaboradores"];

$proximos = $equipe["proximos"];

// HEADER
$voltar = Button::create("Voltar")
  ->color("blue")
  ->withIcon("back")
  ->link("listar-equipes/")
  ->render();

$button = Button::create("+ Novo Colaborador")
  ->color("black")
  ->data([
    "action" => "colaborador:novo",
    "equipe-id" => $equipe["id"]
  ]);

$header = Header::create("Colaboradores | {$equipe["nome"]}")
  ->addButton($voltar)
  ->addButton($button)
  ->render();

echo $header;

// INFORBOX
echo $equipe["fila"]["infobox"];

// CARD DO USUÁRIO
$content = <<<HTML
<div class="card__header center">
  <div class="card__header__info">
    <strong>{$equipe['nome']}</strong>
    <div class="subinfo">
      <span>{$equipe['descricao']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$equipe["numero_badge"]}
  {$equipe["produto_badge"]}
  {$equipe["status_badge"]}
</div>
HTML;

echo Card::create($content)->render();

// PRÓXIMOS
$proximosHeader = <<<HTML
<tr>
  <th>Posição</th>
  <th>Usuario</th>
  <th>Número da vez <i class="fa-solid fa-circle-info"></i></th>
</tr>
HTML;

$rows = "";
if ($proximos === null){
  $rows .= <<<HTML
  <tr>
    <td colspan="3">Nenhum usuário na fila de recebimento</td>
  </tr>
  HTML;
} else {
  foreach ($proximos as $key => $proximo) {
    $medalha = "";
    if ($key === 0) $medalha = "🥇";
    else if ($key === 1) $medalha = "🥈";
    else if ($key === 2) $medalha = "🥉";
    $rows .= <<<HTML
    <tr>
      <td>$medalha</td>
      <td>{$proximo["nome"]}</td>
      <td>{$proximo["vez"]}</td>
    </tr>
    HTML;
  }
}

echo Table::create($proximosHeader, "")
  ->withTitle("Próximos na fila")
  ->addRows($rows);

// Começa a criar a tabela de usuários
$tableHeader = <<<HTML
<tr>
  <th>Usuários</th>
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