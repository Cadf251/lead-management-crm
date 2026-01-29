<?php

use App\adms\UI\Button;
use App\adms\UI\Field;
use App\adms\UI\Form;

if (isset($this->data["result"])) {
  var_dump($this->data["result"]->getAlert());
}

echo Button::create("Voltar")
  ->color("black")
  ->link("master/")
  ->render();

$fields = [
  Field::create("Nome da Empresa", "tenant_name")
    ->value($_POST["tenant_name"])
    ->required(),

  Field::create("Email de contato", "tenant_email")
    ->value($_POST["tenant_email"])
    ->required(),

  Field::create("Host", "host")
    ->value($_POST["host"])
    ->required(),

  Field::create("DB User", "db_user")
    ->value($_POST["db_user"])
    ->required(),

  Field::create("DB Name", "db_name")
    ->value($_POST["db_name"])
    ->required()
];

echo Form::create("criar-servidor")
  ->addFields($fields)
  ->withTitle("Criar nova empresa")
  ->render();