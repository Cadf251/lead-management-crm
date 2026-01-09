<?php

use App\adms\Helpers\CSRFHelper;
use App\adms\Helpers\GenerateLog;
use App\adms\UI\Field;

$csrf = CSRFHelper::generateCSRFToken("form_produto");

if (!is_array($product)){
  GenerateLog::generateLog(GenerateLog::ERROR, "View var is bad formated", ["product" => $product]);
  unset($product);
}

return [
  Field::create("Nome do produto", "name")
    ->value($product["name"] ?? "")
    ->maxLength(100)
    ->required(),

  Field::create("Descrição do produto", "description")
    ->type(Field::TYPE_TEXTAREA)
    ->value($product["description"] ?? "")
    ->maxLength(255),
    
  Field::create("", "csrf_token")
    ->type(Field::TYPE_HIDDEN)
    ->value($csrf)
];