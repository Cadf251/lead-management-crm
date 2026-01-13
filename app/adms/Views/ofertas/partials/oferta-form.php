<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Field;

$csrf = CSRFHelper::generateCSRFToken("form_oferta");

return [
  Field::create("Nome da oferta", "name")
    ->value($offer["name"] ?? "")
    ->maxLength(100)
    ->required(),

  Field::create("Descrição da oferta", "description")
    ->type(Field::TYPE_TEXTAREA)
    ->value($offer["description"] ?? "")
    ->maxLength(255),
    
  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value($csrf)
];