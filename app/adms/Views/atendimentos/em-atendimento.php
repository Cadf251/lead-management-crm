<?php

use App\adms\UI\Button;
use App\adms\UI\Field;
use App\adms\UI\Header;

echo Header::create("Em Atendimento")
  ->addButton(
    Field::create("", "filtro")
      ->type(Field::TYPE_SELECT)
      ->inputOnly()
      ->withoutDefaultOption()
      ->options(<<<HTML
      <option>A fazer</option>
      <option>Futuro</option>
      HTML)
  )
  ->addButton(
    Button::create("+ Novo")
      ->color("black")
      ->data(["action" => "atendimento:criar"])
  );