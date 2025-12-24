<?php

use App\adms\UI\Button;
use App\adms\UI\Card;
use App\adms\UI\Header;
use App\adms\UI\Table;

$equipe = $this->data["equipes"][0];

$colaboradores = $equipe["colaboradores"];

$proximos = $equipe["proximos"];

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
  {$equipe["produto_badge"]}
  {$equipe["status_badge"]}
</div>
HTML;


if ($equipe["fila"]["infobox"] !== null) {
  echo $equipe["fila"]["infobox"];
}

echo Card::create($content)->render();

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
}

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

$table = Table::create($tableHeader, "table--colaboradores")
  ->addRows($rows)
  ->render();

echo $table;