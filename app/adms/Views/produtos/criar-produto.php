<?php

use App\adms\UI\Form;

$fields = require "partials/produto-form.php";

return Form::create("criar-produto")
  ->addFields($fields)
  ->withTitle("Criar produto")
  ->render();