<?php

use App\adms\UI\Button;
use App\adms\UI\Header;
use App\adms\UI\InfoBox;

echo Header::create("Gerenciar Produtos")
  ->addButton(
    Button::create("+ Criar")
      ->color("black")
      ->data([
        "action" => "product:create"
      ])
  );

$products = $this->data["products"];

if ($products === null) {
  echo InfoBox::create("Nenhum produto", "Você pode criar novos produtos clicando no botão + criar.")
    ->setType(InfoBox::TYPE_WARN);
  return;
}

foreach ($products as $product) {
  echo require "partials/produto-card.php";
}