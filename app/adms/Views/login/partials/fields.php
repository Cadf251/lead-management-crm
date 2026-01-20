<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Field;

$nova = $nova ?? "";
$csrf = $csrf ?? "form_login";

return [
  Field::create("Código da empresa", "server_id")
    ->type(Field::TYPE_NUMBER)
    ->autocomplete("organization")
    ->required(),

  Field::create("Email", "email")
    ->type(Field::TYPE_EMAIL)
    ->autocomplete("username")
    ->required(),

  Field::create("{$nova}Senha", "password")
    ->type(Field::TYPE_PASS)
    ->autocomplete("current_password")
    ->required(),

  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value(CSRFHelper::generateCSRFToken($csrf)),
];