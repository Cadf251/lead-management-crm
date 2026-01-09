<?php

use App\adms\UI\Form;

$product = $this->data["product"];

$fields = require "partials/produto-form.php";

return Form::create("editar-produto/{$product['id']}")
  ->addFields($fields)
  ->withTitle("Editar produto")
  ->render();