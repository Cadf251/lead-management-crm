<?php

use App\adms\UI\Button;
use App\adms\UI\Card;
use App\adms\UI\Header;
use App\adms\UI\InfoBox;

echo Header::create("Gerenciar Ofertas")
  ->addButton(
    Button::create("+ Criar")
      ->color("black")
      ->data([
        "action" => "oferta:criar"
      ])
  );

$ofertas = $this->data["ofertas"];

if ($ofertas === null) {
  echo InfoBox::create("Nenhuma oferta", "Você pode criar novas ofertas clicando no botão + criar.")
    ->setType(InfoBox::TYPE_WARN);
}

foreach ($ofertas as $oferta) {
  echo Card::create(<<<HTML
  
  HTML);
}

