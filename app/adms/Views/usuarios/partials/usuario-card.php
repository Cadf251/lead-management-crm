<?php

use App\adms\UI\Badge;
use App\adms\UI\Card;

$btns = "";

foreach($usuario["button"] as $button){
  $btns .= $button->render();
}

$badge1 = Badge::create($usuario['nivel_nome'], "silver")
  ->tooltip($usuario['nivel_descricao']);

$badge2 = Badge::create($usuario["status_nome"], $usuario["status_class"])
  ->tooltip($usuario['status_descricao']);

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
  $badge1
  $badge2
</div>
<div class="card__inline-items">
  $btns
</div>
HTML;

$final = Card::create($content);

return <<<HTML
<div class="card--{$usuario['id']}">
  $final
</div>
HTML;