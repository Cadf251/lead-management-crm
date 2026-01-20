<?php

use App\adms\UI\Card;
use App\adms\UI\Hamburguer;

$hamb = Hamburguer::create(
  $user["button"]
);

$content = <<<HTML
<div class="card__header center">
  <div class="foto">
    {$user["foto_perfil"]}
  </div>
  <div class="card__header__info">
    <strong>{$user['nome']}</strong>
    <div class="subinfo">
      <span>{$user['email']}</span>
      <span>{$user['celular']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$user["nivel_badge"]}
  {$user["status_badge"]}
</div>
<div class="card__inline-items">
  $hamb
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$user['id']}">
  $final
</div>
HTML;