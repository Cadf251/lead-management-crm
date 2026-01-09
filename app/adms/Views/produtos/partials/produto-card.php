<?php

use App\adms\UI\Button;
use App\adms\UI\Card;

if (!isset($product)) return "";

$button1 = Button::create("")
  ->withIcon("pencil")
  ->tooltip("Editar produto")
  ->data([
    "action" => "product:edit",
    "product-id" => $product["id"]
  ]);

$button2 = Button::create("")
  ->withIcon("trash-can")
  ->tooltip("Excluir produto")
  ->color("red")
  ->data([
    "action" => "product:delete",
    "product-id" => $product["id"],
    "product-name" => $product['name']
  ]);

$card = Card::create(<<<HTML
<div class="card__header center">
  <div class="card__header__info">
    <strong>{$product['name']}</strong>
    <div class="subinfo">
      <span>{$product['description']}</span>
    </div>
  </div>
</div>
<div class="card__inline-items">
  {$button1}
  {$button2}
</div>
HTML);

return <<<HTML
<div class="card--{$product['id']}">
  $card
</div>
HTML;