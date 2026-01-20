<?php

use App\adms\UI\Card;
use App\adms\UI\Hamburguer;

$btns = "";

foreach($team["buttons"] as $button){
  $btns .= $button->render();
}

$hamburguer = Hamburguer::create($btns);

$badge3 = $team["fila"]["badge"];

$content = <<<HTML
<div class="card__header center">
  <div class="card__header__info">
    <strong>{$team['nome']}</strong>
    <div class="subinfo">
      <span>{$team['descricao']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$team["numero_badge"]}
  {$team["status_badge"]}
  $badge3
</div>
<div class="card__inline-items">
  <!-- $btns -->
   $hamburguer
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$team['id']}">
  $final
</div>
HTML;