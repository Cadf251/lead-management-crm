<?php

use App\adms\UI\Badge;
use App\adms\UI\Card;

$btns = "";

foreach($equipe["buttons"] as $button){
  $btns .= $button->render();
}

$badge3 = $equipe["fila"]["badge"];

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
  {$equipe["status_badge"]}
  $badge3
</div>
<div class="card__inline-items">
  $btns
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$equipe['id']}">
  $final
</div>
HTML;