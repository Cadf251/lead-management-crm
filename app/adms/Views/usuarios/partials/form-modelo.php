<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\UI\Field;

$csrf = CSRFHelper::generateCSRFToken("form_usuario");

return [
  Field::create("Nome completo", "nome")
    ->value($usuario["nome"] ?? "")
    ->required(),

  Field::create("Email de acesso", "email")
    ->value($usuario["email"] ?? "")
    ->required(),

  Field::create("Celular", "celular")
    ->value($usuario["celular"] ?? "")
    ->required()
    ->placeholder("(xx)9xxxx-xxxx"),

  Field::create("📷 Foto de perfil", "foto")
    ->type(Field::TYPE_FILE),

  Field::create("Nível de acesso", "nivel_acesso_id")
  ->type(Field::TYPE_SELECT)
  ->options($this->data["form-options"]),

  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value($csrf)
];