<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Field;

$csrf = CSRFHelper::generateCSRFToken("form_equipe");

$equipe = $this->data["equipe"][0];

return [
  Field::create("Nome da equipe", "nome")
    ->value($equipe["nome"] ?? "")
    ->maxLength(50)
    ->required(),

  Field::create("Descrição da equipe", "email")
    ->type(Field::TYPE_TEXTAREA)
    ->value($equipe["descricao"] ?? "")
    ->maxLength(255),

  Field::create("Produto da equipe", "produto_id")
    ->type(Field::TYPE_SELECT)
    ->options($this->data["produtos-options"]),

  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value($csrf)
];