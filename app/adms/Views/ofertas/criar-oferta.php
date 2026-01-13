<?php

use App\adms\UI\Form;

$offer = null;

$fields = require "partials/oferta-form.php";

return Form::create("criar-oferta")
  ->addFields($fields)
  ->withTitle("Criar oferta")
  ->render();