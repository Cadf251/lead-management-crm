<?php

use App\adms\UI\Form;

$fields = require "partials/form-modelo.php";

return Form::create("equipes/criar")
  ->addFields($fields)
  ->withTitle("Criar equipe")
  ->isAjax()
  ->render();