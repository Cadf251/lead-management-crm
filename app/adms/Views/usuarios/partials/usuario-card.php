<?php

use App\adms\UI\Card;

$content = <<<HTML
<div class="card__header center">
  <div class="foto">
    {$usuario["foto_perfil"]}
  </div>
  <div class="card__header__info">
    <strong>{$usuario['nome']}</strong>
    <div class="subinfo">
      <span>{$usuario['email']}</span>
      <span>{$usuario['celular']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$usuario["nivel_badge"]}
  {$usuario["status_badge"]}
</div>
<div class="card__inline-items">
  {$usuario["button"]}
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$usuario['id']}">
  $final
</div>
HTML;