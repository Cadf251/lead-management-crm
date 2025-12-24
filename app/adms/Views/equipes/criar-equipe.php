<?php

use App\adms\UI\Form;

$fields = require "partials/form-modelo.php";

return Form::create("criar-equipe")
  ->addFields($fields)
  ->withTitle("Criar equipe")
  ->render();